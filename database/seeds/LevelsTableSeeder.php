<?php

use Illuminate\Database\Seeder;
use App\Level;

class LevelsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $level1 = new Level;
        $level1->level = 'Superadmin';
        $level1->status_aktif = '1';
        $level1->save();

        $level2 = new Level;
        $level2->level = 'Administrator';
        $level2->status_aktif = '1';
        $level2->save();

        $level3 = new Level;
        $level3->level = 'Konsumen';
        $level3->status_aktif = '1';
        $level3->save();
    }
}
