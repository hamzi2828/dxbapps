<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CreateDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $database = env('DB_DATABASE');
        $charset = 'utf8mb4';
        $collation = 'utf8mb4_unicode_ci';
        
        DB::statement("CREATE DATABASE IF NOT EXISTS `$database` CHARACTER SET $charset COLLATE $collation");
    }
}