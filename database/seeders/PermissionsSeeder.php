<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $permisos = [];
        array_push($permisos, Permission::create(['name' => 'Crear']));
        array_push($permisos, Permission::create(['name' => 'Leer']));

        $administradorRole = Role::create(['name' => 'Agronomo']);

        $administradorRole->syncPermissions($permisos); //en caso de querer darle 1 solo permiso a un rol

        Role::create(['name' => 'Gerente']);
        Role::create(['name' => 'Auxiliar']);

        $userSuperAdmin = User::create([
            'name' => 'Cristian Segura',
            'email' => 'cristian@gmail.com',
            'password' => Hash::make('admin123'),
        ]);

        //asignando rol
        $userSuperAdmin->assignRole('Agronomo');
    }
}
