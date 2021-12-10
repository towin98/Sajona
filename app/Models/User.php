<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens;
    use Notifiable;
    use HasRoles;
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Reglas de validación para el recurso crear.
     *
     * @var array
     */
    public static $messagesValidators = [
        'password.required'                   => 'La contraseña es requerido.',
        'password.min'                        => 'La contraseña debe tener minimo 8 carácteres.',
        'email.required'                      => 'El correo electronico es requerido.',
        'email.email'                         => 'El correo electronico no es válido.',
        'email.max'                           => 'El correo electronico no puede superar los 50 carácteres.',
    ];
}
