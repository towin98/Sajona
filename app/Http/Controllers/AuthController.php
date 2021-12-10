<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;


class AuthController extends Controller
{
    /**
     * Valida credencial del inicio de sesion.
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request)
    {
        $reglas =  [
            'email'     => 'required|email|max:50',
            'password'  => 'required|min:8',
        ];

        $objValidator = Validator::make($request->all(),  $reglas, User::$messagesValidators);
        if($objValidator->fails()){
            return response()->json([
                'message' => 'Error de Validación de Datos',
                'errors'  => $objValidator->errors()
            ], 409);
        }

        $user = User::where('email', $request->email)->first();

        if (! $user || ! Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => [
                    'Las credenciales proporcionadas son incorrectas.'
                ],
            ],409);
        }

        $token = $user->createToken('token')->plainTextToken;

        return response()->json([
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 200);
    }

    /**
     * Cierre de sessión.
     *
     * @param Request $request
     * @return void
     */
    public function logout(Request $request){
        $request->user()->tokens()->delete();
        return [
            'message' => 'logged out'
        ];
    }
}
