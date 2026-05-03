<?php

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'admin1',
            'phone' => '0786412454',
            'email' => 'admin1@apollo.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Admin,
        ]);

        User::factory()->create([
            'name' => 'cleaner1',
            'phone' => '0786412454',
            'email' => 'cleaner1@apollo.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Cleaner,
        ]);

        User::factory()->create([
            'name' => 'customer1',
            'phone' => '0786412454',
            'email' => 'customer1@apollo.com',
            'password' => bcrypt('password'),
            'role' => UserRole::Customer,
        ]);

        User::factory()->count(2)->create(['role' => UserRole::Admin]);
        User::factory()->count(5)->create(['role' => UserRole::Cleaner]);
        User::factory()->count(30)->create(['role' => UserRole::Customer]);
    }
}
