<?php

use Illuminate\Database\Seeder;
use App\User;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user1 = new User;
    	$user1->level_id = '1';
    	$user1->name = 'SuperAdmin';
    	$user1->email = 'superadmin@mail.com';
    	$user1->password = bcrypt('123456');
    	$user1->save();
    }
}
