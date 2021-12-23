<?php

namespace Tests\Feature\Http\Controllers\Propagacion;

use App\Models\Propagacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class PropagacionControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Método que testea que se cree correctamente Propagación.
     *
     * @return void
     */
    public function test_guardar_propagacion_correct()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/api/propagacion',[
            "pro_fecha"                     => date("Y-m-d H:i:s"),
            "pro_tipo_propagacion"          => "Esqueje",
            "pro_variedad"                  => 2,
            "pro_tipo_incorporacion"        => "PROPIA",
            "pro_cantidad_material"         => 200,
            "pro_cantidad_plantas_madres"   => 110,
        ]);

        $response->assertValid();

        // $response->assertUnprocessable();
        $response->assertStatus(201);
    }

    /**
     * Método que testea validaciones de data enviada al controller, el test pasa si devuelve status 422.
     *
     * @return void
     */
    public function test_validacion_guardar_propagacion_errores()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/api/propagacion',[
            "pro_fecha"                     => date("Y-m-d H:i:s"),
            "pro_tipo_propagacion"          => "Esqueje",
            "pro_variedad"                  => 2,
            "pro_tipo_incorporacion"        => "Propia",
            "pro_cantidad_material"         => "13e22",
            "pro_cantidad_plantas_madres"   => 837,
        ]);

        $response->assertUnprocessable(); // si devuelve 422 pasa la prueba
        // $response->assertValid();
    }

    /**
     * Lista data de propagacion correctamente, se valida estructura y el status de respuesta.
     *
     * @return void
     */
    public function test_muestra_los_datos_de_propagacion_correctamente()
    {
        $this->withoutExceptionHandling();

        Propagacion::factory(20)->create();

        $response = $this->get('api/propagacion/listar?length=3&start=0');

        $response->assertJsonStructure([
            'data'=> [
                '*' => [
                    'pro_id_lote',
                    'pro_fecha',
                    'pro_tipo_propagacion',
                    'pro_variedad',
                    'pro_tipo_incorporacion',
                    'pro_cantidad_material',
                    'pro_cantidad_plantas_madres',
                    'pro_estado',
                    'created_at',
                    'updated_at',
                ]
            ],
            'filtrados',
            'total'
        ]);

        $response->assertStatus(200);
    }
}
