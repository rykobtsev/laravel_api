<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            ['id' => 1, 'name' => 'root'],
            ['id' => 2, 'name' => 'admin'],
            ['id' => 3, 'name' => 'users'],
            ['id' => 4, 'name' => 'guest'],
        ]);
    }
}