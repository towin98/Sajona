<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PermissionController extends Controller
{
    /**
     * Verifica si el usuario tiene alguno de los roles.
     *
     * @param Request $request
     * @return boolean
     */
    public function canRol(Request $request) {

        $users = User::findOrFail(Auth::user()->id);

        $vRoles = explode(',',$request->roles);

        for ($nR=0; $nR < count($vRoles); $nR++) {
            // Pregunta si tiene el rol
            if ($users->hasRole($vRoles[$nR])) {
                return response()->json([
                    'data' => true
                ], 200);
            }
        }

        // Retorno false no tiene rol.
        return response()->json([
            'data' => false
        ], 200);
    }

    /**
     * Retorna nombre que tiene el usuario autenticado.
     *
     * @return string
     */
    public function buscaNombreRolUser() {

        $users = User::findOrFail(Auth::user()->id);
        $cRol = $users->getRoleNames();

        return response()->json([
            'message' => 'Nombre de rol autenticado',
            'data' => $cRol[0]
        ], 200);
    }
}



        // $permisos = $users->getAllPermissions()->pluck('name');

        // foreach ($permisos as $permiso) {
        //     if ($permiso == $request->permiso) {
        //         return response()->json([
        //             'data' => true
        //         ], 200);
        //     }
        // }
