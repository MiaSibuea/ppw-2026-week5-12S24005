<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class PustakawanSeeder extends Seeder
{
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'pustakawan@del.ac.id'],
            [
                'name' => 'Pustakawan SIPUS-Del',
                'password' => 'password123',
            ],
        );
    }
}
