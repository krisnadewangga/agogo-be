<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class BackupDatabaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $menu_active = "item|backup_db|0";
        $tables = $this->getTableNames();

        $disk = Storage::disk('local');
        if (!$disk->exists('backups')) {
            $disk->makeDirectory('backups');
        }

        $files = collect($disk->files('backups'))
            ->filter(function ($file) {
                return strtolower(pathinfo($file, PATHINFO_EXTENSION)) === 'sql';
            })
            ->map(function ($file) use ($disk) {
                return [
                    'name' => basename($file),
                    'size' => $disk->size($file),
                    'last_modified' => $disk->lastModified($file),
                ];
            })
            ->sortByDesc('last_modified')
            ->values();

        return view('backup.index', compact('menu_active', 'files', 'tables'));
    }

    public function store(Request $request)
    {
        $connection = config('database.default');
        $driver = config("database.connections.$connection.driver");
        $databaseName = config("database.connections.$connection.database");

        if ($driver !== 'mysql') {
            return redirect()->back()->with('error', 'Backup hanya didukung untuk database MySQL.');
        }

        try {
            @set_time_limit(0);

            $disk = Storage::disk('local');
            if (!$disk->exists('backups')) {
                $disk->makeDirectory('backups');
            }

            $allTables = $this->getTableNames();
            $tables = $allTables;

            if (empty($tables)) {
                return redirect()->back()->with('error', 'Tidak ada tabel untuk dibackup.');
            }

            $pdo = DB::connection()->getPdo();

            $dump = [];
            $dump[] = '-- AGOGO Bakery Database Backup';
            $dump[] = '-- Created at: ' . date('Y-m-d H:i:s');
            $dump[] = '-- Database: ' . $databaseName;
            $dump[] = 'SET FOREIGN_KEY_CHECKS=0;';
            $dump[] = '';

            foreach ($tables as $table) {
                $createResult = DB::select("SHOW CREATE TABLE `{$table}`");
                if (empty($createResult)) {
                    continue;
                }

                $createData = (array) $createResult[0];
                $createSql = isset($createData['Create Table']) ? $createData['Create Table'] : array_values($createData)[1];

                $dump[] = '-- Table: ' . $table;
                $dump[] = 'DROP TABLE IF EXISTS `' . $table . '`;';
                $dump[] = $createSql . ';';

                $rows = DB::table($table)->get();
                foreach ($rows as $row) {
                    $rowData = (array) $row;
                    $columns = array_keys($rowData);
                    $values = array_map(function ($value) use ($pdo) {
                        if (is_null($value)) {
                            return 'NULL';
                        }
                        if (is_bool($value)) {
                            return $value ? '1' : '0';
                        }
                        if (is_int($value) || is_float($value)) {
                            return (string) $value;
                        }
                        return $pdo->quote((string) $value);
                    }, array_values($rowData));

                    $quotedColumns = array_map(function ($column) {
                        return '`' . $column . '`';
                    }, $columns);

                    $dump[] = 'INSERT INTO `' . $table . '` (' . implode(', ', $quotedColumns) . ') VALUES (' . implode(', ', $values) . ');';
                }

                $dump[] = '';
            }

            $dump[] = 'SET FOREIGN_KEY_CHECKS=1;';

            $fileName = 'backup-all-' . date('Ymd-His') . '.sql';
            $relativePath = 'backups/' . $fileName;
            $disk->put($relativePath, implode(PHP_EOL, $dump));

            return redirect()->back()->with('success', 'Backup database berhasil dibuat: ' . $fileName);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Backup gagal: ' . $e->getMessage());
        }
    }

    public function download(Request $request)
    {
        $request->validate([
            'file_name' => 'required|string',
        ]);

        $safeFile = basename($request->input('file_name'));
        $relativePath = 'backups/' . $safeFile;

        if (!Storage::disk('local')->exists($relativePath)) {
            abort(404, 'File backup tidak ditemukan.');
        }

        return response()->download(storage_path('app/' . $relativePath));
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'file_name' => 'required|string',
        ]);

        $safeFile = basename($request->input('file_name'));
        $relativePath = 'backups/' . $safeFile;

        if (!Storage::disk('local')->exists($relativePath)) {
            return redirect()->back()->with('error', 'File backup tidak ditemukan.');
        }

        try {
            Storage::disk('local')->delete($relativePath);

            return redirect()->back()->with('success', 'File backup berhasil dihapus: ' . $safeFile);
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', 'Gagal menghapus file backup: ' . $e->getMessage());
        }
    }

    public function restore(Request $request)
    {
        $request->validate([
            'file_name' => 'required|string',
            'restore_table' => 'required|string',
        ]);

        $connection = config('database.default');
        $driver = config("database.connections.$connection.driver");
        if ($driver !== 'mysql') {
            return redirect()->back()->with('error', 'Restore hanya didukung untuk database MySQL.');
        }

        try {
            @set_time_limit(0);
            $startedAt = microtime(true);

            $safeFile = basename($request->input('file_name'));
            $relativePath = 'backups/' . $safeFile;
            $targetTable = $request->input('restore_table', '__ALL__');
            $allTables = $this->getTableNames();

            if ($targetTable !== '__ALL__' && !in_array($targetTable, $allTables, true)) {
                return redirect()->back()->with('error', 'Tabel restore tidak valid.');
            }

            if (!Storage::disk('local')->exists($relativePath)) {
                return redirect()->back()->with('error', 'File backup tidak ditemukan.');
            }

            $filePath = storage_path('app/' . $relativePath);
            if (!is_readable($filePath) || filesize($filePath) === 0) {
                return redirect()->back()->with('error', 'File backup kosong atau tidak bisa dibaca.');
            }

            DB::connection()->disableQueryLog();
            DB::statement('SET FOREIGN_KEY_CHECKS=0');
            $restoreStats = [
                'statements' => 0,
                'tables' => [],
            ];
            $this->executeSqlDumpFromFile($filePath, $targetTable === '__ALL__' ? null : $targetTable, $restoreStats);
            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            $duration = round(microtime(true) - $startedAt, 2);
            $restoreLogs = [
                'File backup: ' . $safeFile,
                'Mode restore: ' . ($targetTable === '__ALL__' ? 'Semua tabel' : 'Tabel ' . $targetTable),
                'Total statement dijalankan: ' . number_format($restoreStats['statements']),
                'Durasi proses: ' . $duration . ' detik',
            ];

            if (!empty($restoreStats['tables'])) {
                ksort($restoreStats['tables']);

                foreach ($restoreStats['tables'] as $tableName => $count) {
                    $restoreLogs[] = 'Tabel ' . $tableName . ': ' . number_format($count) . ' statement';
                }
            }

            if ($targetTable === '__ALL__') {
                return redirect()->back()
                    ->with('success', 'Restore database berhasil dijalankan dari: ' . $safeFile)
                    ->with('restore_logs', $restoreLogs);
            }

            return redirect()->back()
                ->with('success', 'Restore tabel ' . $targetTable . ' berhasil dijalankan dari: ' . $safeFile)
                ->with('restore_logs', $restoreLogs);
        } catch (\Throwable $e) {
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Throwable $inner) {
                // no-op
            }
            return redirect()->back()->with('error', 'Restore gagal: ' . $e->getMessage());
        }
    }

    public function truncate(Request $request)
    {
        $request->validate([
            'table_name' => 'required|string',
        ]);

        $target = $request->input('table_name');
        $tables = $this->getTableNames();

        if ($target !== '__ALL__' && !in_array($target, $tables, true)) {
            return redirect()->back()->with('error', 'Tabel tidak valid.');
        }

        try {
            @set_time_limit(0);

            DB::statement('SET FOREIGN_KEY_CHECKS=0');

            if ($target === '__ALL__') {
                foreach ($tables as $table) {
                    DB::unprepared('TRUNCATE TABLE `' . str_replace('`', '``', $table) . '`');
                }
            } else {
                DB::unprepared('TRUNCATE TABLE `' . str_replace('`', '``', $target) . '`');
            }

            DB::statement('SET FOREIGN_KEY_CHECKS=1');

            if ($target === '__ALL__') {
                return redirect()->back()->with('success', 'Semua data tabel berhasil dihapus.');
            }

            return redirect()->back()->with('success', 'Data tabel ' . $target . ' berhasil dihapus.');
        } catch (\Throwable $e) {
            try {
                DB::statement('SET FOREIGN_KEY_CHECKS=1');
            } catch (\Throwable $inner) {
                // no-op
            }
            return redirect()->back()->with('error', 'Hapus data tabel gagal: ' . $e->getMessage());
        }
    }

    private function getTableNames()
    {
        return collect(DB::select('SHOW TABLES'))
            ->map(function ($row) {
                return array_values((array) $row)[0];
            })
            ->values()
            ->all();
    }

    private function executeSqlDump($sql)
    {
        $statement = '';
        $lines = preg_split('/\r\n|\r|\n/', $sql);

        foreach ($lines as $line) {
            $trimmed = trim($line);

            if ($trimmed === '' || strpos($trimmed, '--') === 0 || strpos($trimmed, '/*') === 0 || strpos($trimmed, '*/') === 0) {
                continue;
            }

            $statement .= $line . PHP_EOL;

            if (substr(rtrim($line), -1) === ';') {
                DB::unprepared($statement);
                $statement = '';
            }
        }

        if (trim($statement) !== '') {
            DB::unprepared($statement);
        }
    }

    private function executeSqlDumpFromFile($filePath, $targetTable = null, array &$restoreStats = [])
    {
        $statement = '';
        $inBlockComment = false;
        $file = new \SplFileObject($filePath, 'r');

        while (!$file->eof()) {
            $line = (string) $file->fgets();
            $trimmed = trim($line);

            if ($inBlockComment) {
                if (strpos($trimmed, '*/') !== false) {
                    $inBlockComment = false;
                }
                continue;
            }

            if ($trimmed === '') {
                continue;
            }

            if (strpos($trimmed, '/*') === 0) {
                if (strpos($trimmed, '*/') === false) {
                    $inBlockComment = true;
                }
                continue;
            }

            if (strpos($trimmed, '--') === 0) {
                continue;
            }

            $statement .= $line;

            if (substr(rtrim($line), -1) === ';') {
                $sql = trim($statement);
                if ($sql !== '' && $this->shouldExecuteStatement($sql, $targetTable)) {
                    DB::unprepared($sql);
                    $restoreStats['statements'] = ($restoreStats['statements'] ?? 0) + 1;

                    $tableName = $this->extractTableNameFromStatement($sql);
                    if ($tableName !== null) {
                        if (!isset($restoreStats['tables'][$tableName])) {
                            $restoreStats['tables'][$tableName] = 0;
                        }

                        $restoreStats['tables'][$tableName]++;
                    }
                }
                $statement = '';
            }
        }

        $remaining = trim($statement);
        if ($remaining !== '' && $this->shouldExecuteStatement($remaining, $targetTable)) {
            DB::unprepared($remaining);
            $restoreStats['statements'] = ($restoreStats['statements'] ?? 0) + 1;

            $tableName = $this->extractTableNameFromStatement($remaining);
            if ($tableName !== null) {
                if (!isset($restoreStats['tables'][$tableName])) {
                    $restoreStats['tables'][$tableName] = 0;
                }

                $restoreStats['tables'][$tableName]++;
            }
        }
    }

    private function extractTableNameFromStatement($sql)
    {
        if (preg_match('/^\s*(INSERT\s+INTO|REPLACE\s+INTO|UPDATE|DELETE\s+FROM|DROP\s+TABLE(?:\s+IF\s+EXISTS)?|CREATE\s+TABLE(?:\s+IF\s+NOT\s+EXISTS)?|ALTER\s+TABLE|TRUNCATE\s+TABLE)\s+`?([A-Za-z0-9_]+)`?/i', $sql, $matches)) {
            return $matches[2];
        }

        return null;
    }

    private function shouldExecuteStatement($sql, $targetTable = null)
    {
        if ($targetTable === null) {
            return true;
        }

        if (preg_match('/^\s*SET\s+FOREIGN_KEY_CHECKS\s*=\s*[01]\s*;?$/i', $sql)) {
            return false;
        }

        if (preg_match('/^\s*(INSERT\s+INTO|REPLACE\s+INTO|UPDATE|DELETE\s+FROM|DROP\s+TABLE(?:\s+IF\s+EXISTS)?|CREATE\s+TABLE(?:\s+IF\s+NOT\s+EXISTS)?|ALTER\s+TABLE|TRUNCATE\s+TABLE)\s+`?([A-Za-z0-9_]+)`?/i', $sql, $matches)) {
            return strtolower($matches[2]) === strtolower($targetTable);
        }

        return false;
    }
}
