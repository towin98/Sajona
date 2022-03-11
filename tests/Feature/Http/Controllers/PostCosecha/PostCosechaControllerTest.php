<?php

namespace Tests\Feature\Http\Controllers\PostCosecha;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PostCosechaControllerTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function buscaPostCosechasYLasMostraCorrectamente()
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
