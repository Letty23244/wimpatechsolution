<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        User::create([
            'name' => 'WiMPa Admin',
            'email' => 'admin@wimpatechsolutions.com',
            'password' => bcrypt('admin@256'),
            'role' => 'admin',
        ]);
    }
}