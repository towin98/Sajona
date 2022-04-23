<?php
namespace App\Traits;

use App\Models\User;
use Illuminate\Support\Facades\Auth;

trait commonsTrait {
    /**
     * Retorna true o false si el usuario tiene el permiso.
     *
     * @return string
     */
    public function fnVerificaPermisoUsuario($pPermiso){
        $users = User::findOrfail(Auth::user()->id);
        $permisos = $users->getAllPermissions()->pluck('name');
        foreach ($permisos as $permiso) {
            if ($pPermiso == $permiso) {
                return true;
            }
        }
        return false;
    }
}
?>
