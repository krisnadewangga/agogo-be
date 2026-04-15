@extends('layouts.app1')

@section('content')
    @component('components.card', ['title' => 'Backup Database',
                                   'breadcumbs' => array(
                                                      array('judul' => 'Backup Database','link' => '#')
                                                    )
                                  ])
        <div class="card">
            @if (session('success'))
                <div class="alert alert-success" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="alert alert-danger" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            @if (session('restore_logs'))
                <div class="alert alert-info" role="alert">
                    <strong>Ringkasan restore:</strong>
                    <ul style="margin-top: 10px; margin-bottom: 0; padding-left: 20px;">
                        @foreach(session('restore_logs') as $log)
                            <li>{{ $log }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('backup_database.store') }}">
                @csrf
                <div style="padding-top: 5px;">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa fa-database"></i> Buat Backup Database
                    </button>
                </div>
            </form>

            <hr>

            <form method="POST" action="{{ route('backup_database.restore') }}" id="restore-backup-form">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group @error('file_name') has-error @enderror">
                            <label>Restore dari Backup Lokal</label>
                            <select name="file_name" class="form-control" required>
                                <option value="">Pilih Backup</option>
                                @foreach($files as $file)
                                    <option value="{{ $file['name'] }}">{{ $file['name'] }}</option>
                                @endforeach
                            </select>
                            @error('file_name')
                                <label class="control-label" for="inputError">
                                    <i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                                </label>
                            @enderror
                        </div>

                        <div class="form-group @error('restore_table') has-error @enderror">
                            <label>Restore Tabel</label>
                            <select name="restore_table" class="form-control" required>
                                <option value="__ALL__">Semua Tabel</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table }}">{{ $table }}</option>
                                @endforeach
                            </select>
                            @error('restore_table')
                                <label class="control-label" for="inputError">
                                    <i class="fa fa-times-circle-o"></i> <strong>{{ $message }}</strong>
                                </label>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-6" style="padding-top: 25px;">
                        <button type="submit" class="btn btn-warning" onclick="return confirm('Restore akan menimpa data sesuai isi backup lokal yang dipilih. Lanjutkan?')">
                            <i class="fa fa-upload"></i> Restore Database
                        </button>
                    </div>
                </div>
            </form>

            <div id="restore-loading" class="alert alert-warning" style="display:none; margin-top: 15px;">
                <i class="fa fa-spinner fa-spin"></i> Proses restore sedang berjalan, mohon tunggu sampai selesai.
            </div>

            <hr>

            <form method="POST" action="{{ route('backup_database.truncate') }}">
                @csrf
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Hapus Data Tabel</label>
                            <select name="table_name" class="form-control" required>
                                <option value="">Pilih Tabel</option>
                                <option value="__ALL__">Semua Tabel</option>
                                @foreach($tables as $table)
                                    <option value="{{ $table }}">{{ $table }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6" style="padding-top: 25px;">
                        <button type="submit" class="btn btn-danger" onclick="return confirm('Data tabel yang dipilih akan dihapus permanen. Lanjutkan?')">
                            <i class="fa fa-trash"></i> Hapus Data Tabel
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card" style="margin-top: 10px;">
            <h4>Riwayat Backup</h4>
            <div class="table-responsive" style="margin-top: 10px;">
                <table class="table table-bordered" id="table_backup_db">
                    <thead>
                        <tr>
                            <th style="width: 5px;">No</th>
                            <th>Nama File</th>
                            <th class="text-right">Ukuran</th>
                            <th>Waktu Backup</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($files as $file)
                            <tr>
                                <td></td>
                                <td>{{ $file['name'] }}</td>
                                <td class="text-right">{{ number_format($file['size'] / 1024, 2, ',', '.') }} KB</td>
                                <td>{{ date('d/m/Y H:i:s', $file['last_modified']) }}</td>
                                <td>
                                    <form action="{{ route('backup_database.download') }}" method="POST" style="display:inline-block;">
                                        @csrf
                                        <input type="hidden" name="file_name" value="{{ $file['name'] }}">
                                        <button type="submit" class="btn btn-success btn-xs">
                                            <i class="fa fa-download"></i> Download
                                        </button>
                                    </form>
                                    <form action="{{ route('backup_database.destroy') }}" method="POST" style="display:inline-block;" onsubmit="return confirm('File backup akan dihapus permanen. Lanjutkan?')">
                                        @csrf
                                        <input type="hidden" name="file_name" value="{{ $file['name'] }}">
                                        <button type="submit" class="btn btn-danger btn-xs">
                                            <i class="fa fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <script type="text/javascript">
            $(function(){
                var table_backup_db = $("#table_backup_db").DataTable({
                    "ordering": false
                });

                table_backup_db.on('order.dt search.dt', function () {
                    table_backup_db.column(0, {search:'applied', order:'applied'}).nodes().each(function (cell, i) {
                        cell.innerHTML = i+1;
                    });
                }).draw();

                $('#restore-backup-form').on('submit', function () {
                    $('#restore-loading').show();
                    $(this).find('button[type="submit"]').prop('disabled', true);
                });
            });
        </script>
    @endcomponent
@endsection
