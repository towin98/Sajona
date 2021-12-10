<?php

namespace Tests\Feature\Http\Controllers\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    // Cada test refresca la base de datos
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_login_load_correctly()
    {
        // Errores mas detallados
        // $this->withoutExceptionHandling();

        $user = User::factory(1)->create();

        $response = $this->post('/api/login', [
            'email'     => 'cristian@gmail.com',
            'password'  => 'admin1235',
        ]);

        // Comprobacion de credenciales.
        $this->assertCredentials([
            'email'     => 'cristian@gmail.com',
            'password'  => 'admin123',
        ]);

        $response->assertStatus(200);
    }
}


