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

        $user = User::factory()->create();
        
        $response = $this->post('/sajona/login', [
            'email'     => $user->email,
            'password'  => 'admin123',
        ]);
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'access_token',
            'token_type'
        ]);

    }
}


