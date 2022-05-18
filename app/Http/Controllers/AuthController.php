<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use App\Mail\RecuperarPassMailable;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Valida credencial del inicio de sesion.
     *
     * @param Request $request
     * @return void
     */
    public function login(Request $request){
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
        }else{
            if (count($user->tokens) > 0) {
                if ($request->closeSesion != "SI") {
                    return response()->json([
                        'message' => 'Ya existe una sesión iniciada',
                        'errors'  => "¿Quieres iniciar sesión aquí?."
                    ], 423);
                }else{
                    // Si ya hay una sesion iniciada y se envia como parametro SI se cierra sesión.
                    $user->tokens()->delete();
                }
            }
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

    /**
     * Método que envia correo electronico con informacion para resetear password.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function resetPassword(Request $request){
        $reglas =  [
            'email'     => 'required|email|max:50'
        ];

        $objValidator = Validator::make($request->all(),  $reglas, User::$messagesValidators);
        if($objValidator->fails()){
            return response()->json([
                'message' => 'Error de Validación de Datos',
                'errors'  => $objValidator->errors()
            ], 422);
        }

        $user = User::where('email', $request->email)->first();
        if (!$user) {
            return response()->json([
                'errors' => "NO fue posible realizar la operación solicitada."
            ], 401);
        }

        try {
            $password = Str::random(8);
            $user->update([
                'password' => Hash::make($password),
            ]);

            $correo = new RecuperarPassMailable($password, $user);
            Mail::to($request->email)->send($correo);

            return response()->json([
                "message" => "Se envío información importante a su correo en relación con los datos de acceso a la plataforma SAJONA"
            ], 200);

        } catch (Exception $e) {
            return response()->json([
                "errors" => "No fue posible realizar la operación solicitada."
            ], 500);
        }
    }

    /**
     * Undocumented function
     *
     * @return void
     */
    public function passwordChange(Request $request){
        $reglas =  [
            'claveActual'            => 'required|min:8',
            'password'               => 'required|confirmed|min:8',
            'password_confirmation'  => 'required|min:8',
        ];

        $objValidator = Validator::make($request->all(),  $reglas, User::$messagesValidatorsCambioClave);
        if($objValidator->fails()){
            return response()->json([
                'message' => 'Error de Validación de Datos',
                'errors'  => $objValidator->errors()
            ], 422);
        }

        $user = Auth::user();

        if (!Hash::check($request->claveActual, $user->password)) {
            return response()->json([
                "message" => "Error de Validación de Datos",
                "errors" => [
                        "claveActual" => [
                            "Contraseña actual Ingresada Incorrecta."
                        ]
                    ]
            ], 422);
        }

        User::findOrFail($user->id)->update([
            "password" => Hash::make($request->password)
        ]);

        return response()->json([
            "message" => "Se cambio exitosamente la contraseña."
        ], 200);
    }
}
