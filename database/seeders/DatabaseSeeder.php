<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Users Roles
        Role::create(['title' => 'Admin']);
        Role::create(['title' => 'Host']);
        Role::create(['title' => 'Buyer']);

        // Admin Account
        User::create([
            'name' => 'Administrator',
            'email' => 'billet.admin@gmail.com',
            'password' => bcrypt('12345678'),
            'remember_token' => Str::random(10),
            'balance' => 0,
            'role' => 1
        ]);
    }
}
