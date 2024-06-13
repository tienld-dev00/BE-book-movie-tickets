<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Enums\UserStatus;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->delete();

        DB::table('users')->truncate();

        DB::table('users')->insert([
            [
                'name'              => 'Admin',
                'email'             => 'admin@gmail.com',
                'phone_number'      => '',
                'avatar'            => '',
                'status'            => UserStatus::ACTIVE,
                'role'              => UserRole::ADMIN,
                'password'          => bcrypt('123123123'),
                'google_id'         => '',
                'facebook_id'       => '',
            ],
        ]);
    }
}
