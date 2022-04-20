<?php

namespace Tests\Feature\Http\Controllers\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

trait AutenticacionTrait
{
    public function CrearRolsPermisos(){
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
            'email' => 'cristian@gmail.com',
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

    public function Autenticacion($email, $pass){

        $this->CrearRolsPermisos();

        $response = $this->post('/sajona/login', [
            "email"             => $email,
            "password"          => $pass
        ]);
        $response->assertStatus(200);
    }
}
