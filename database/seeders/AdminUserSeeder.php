<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'diazmosisjeremy@gmail.com'],
            [
                'name' => 'Jeremy',
                'apellido' => 'Díaz',
                'phone' => '04128296133',
                'password' => Hash::make('Manchester1407+'), 
                'role' => 'admin', 
            ]
        );
    }
}