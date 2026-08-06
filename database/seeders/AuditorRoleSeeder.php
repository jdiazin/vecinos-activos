<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class AuditorRoleSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Crear el permiso exclusivo para ver la auditoría
        $permission = Permission::firstOrCreate(['name' => 'ver auditorias']);

        // 2. Crear el rol de Auditor
        $role = Role::firstOrCreate(['name' => 'Auditor']);

        // 3. Asignar el permiso al rol de Auditor
        $role->givePermissionTo($permission);
    }
}