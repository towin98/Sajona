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
        array_push($permisos, Permission::create(['name' => 'LISTAR']));   // tracking
        array_push($permisos, Permission::create(['name' => 'CREAR']));    // store
        array_push($permisos, Permission::create(['name' => 'EDITAR']));   // update
        array_push($permisos, Permission::create(['name' => 'ELIMINAR'])); // Destroy
        array_push($permisos, Permission::create(['name' => 'VER']));      // deshabilitar campos

        $administradorRole = Role::create(['name' => 'Agronomo']);

        $administradorRole->syncPermissions($permisos); //en caso de querer darle 1 solo permiso a un rol

        Role::create(['name' => 'Gerente']);
        Role::create(['name' => 'Auxiliar']);

        $userSuperAdmin = User::create([
            'name' => 'Cristian Segura',
            'email' => 'segura9801@gmail.com',
            'password' => Hash::make('admin123'),
        ]);
        $userSuperAdmin->assignRole('Agronomo');

        $user = User::create([
            'name' => 'Eduardo Martinez',
            'email' => 'eduardo@gmail.com',
            'password' => Hash::make('admin123'),
        ]);
        $user->assignRole('Gerente');

        $user = User::create([
            'name' => 'Richard Ramirez',
            'email' => 'richard@gmail.com',
            'password' => Hash::make('admin123'),
        ]);
        $user->assignRole('Auxiliar');
    }
}
