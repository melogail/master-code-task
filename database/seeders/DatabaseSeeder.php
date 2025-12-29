<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {

        User::insert([
            [
                'name' => 'Admin',
                'email' => 'admin@email.com',
                'password' => bcrypt('password'),
                'is_admin' => true,
            ],
            [
                'name' => 'Staff',
                'email' => 'staff@email.com',
                'password' => bcrypt('password'),
                'is_admin' => false,
            ],
        ]);

        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            VendorSeeder::class,
            ExpenseSeeder::class,
        ]);
    }
}
