<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \App\Models\User::create([
            'name' => 'Professor Exemplo',
            'email' => 'professor@exemplo.com',
            'password' => \Hash::make('password'),
            'email_verified_at' => now(),
        ]);
    }
}
