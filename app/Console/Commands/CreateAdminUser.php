<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'user:create-admin';
    protected $description = 'Crea o actualiza el usuario administrador por defecto';

    public function handle()
    {
        $user = User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'Jeremy Diaz',
                'password' => Hash::make('Manchester1407+'),
                'role' => 'admin' // Si tu campo de rol se llama diferente, cámbialo aquí
            ]
        );

        $this->info("¡Usuario administrador {$user->email} creado/actualizado con éxito!");
    }
}