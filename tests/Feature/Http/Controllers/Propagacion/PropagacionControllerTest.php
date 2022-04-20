<?php

namespace Tests\Feature\Http\Controllers\Propagacion;

use App\Models\Propagacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Tests\Feature\Http\Controllers\Traits\AutenticacionTrait;

class PropagacionControllerTest extends TestCase
{
    use RefreshDatabase;
    use AutenticacionTrait;

    /**
     * Método que testea que se cree correctamente Propagación.
     *
     * @return void
     */
    public function test_guardar_propagacion_correct()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/sajona/propagacion', [
            "pro_fecha"                     => date("Y-m-d"),
            "pro_tipo_propagacion"          => "Esqueje",
            "pro_variedad"                  => 2,
            "pro_tipo_incorporacion"        => "PROPIA",
            "pro_cantidad_material"         => 200,
            "pro_cantidad_plantas_madres"   => 110,
        ]);

        $response->assertValid();

        // $response->assertUnprocessable();
        $response->assertStatus(201)
            ->assertExactJson([
                'message' => 'Datos Guardados',
            ]);
    }

    /**
     * Método que testea validaciones de data enviada al controller, el test pasa si devuelve status 422.
     *
     * @return void
     */
    public function test_validacion_guardar_propagacion_errores()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('/sajona/propagacion', [
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

        $response = $this->get('sajona/propagacion/listar?length=3&start=0');

        $response->assertJsonStructure([
            'current_page',
            'data' => [
                '*' => [
                    "pro_id_lote",
                    "pro_fecha",
                    "pro_tipo_propagacion",
                    "pro_variedad",
                    "pro_tipo_incorporacion",
                    "pro_cantidad_material",
                    "pro_cantidad_plantas_madres",
                    "pro_estado",
                ]
            ],
            'first_page_url',
            'from',
            'last_page',
            'last_page_url',
            'links' => [
                '*' => [
                    "url",
                    "label",
                    "active",
                ]
            ],
            'next_page_url',
            'path',
            'per_page',
            'prev_page_url',
            'to',
            'total'
        ]);

        $response->assertStatus(200);
    }

    public function test_actualiza_un_registro_de_una_propagacion_correctamente(){
        $this->withoutExceptionHandling();
        // $this->Autenticacion("cristian@gmail.com", "admin123");
        // Creamos un registro de Tipo de propagacion
        $tipoPropagacion = Propagacion::factory(1)->create()->first();

        // Hacemos la solicitud a la url
        $response = $this->put("sajona/propagacion/actualizar/".$tipoPropagacion->pro_id_lote, [
                "pro_fecha"                     => "2022-04-05",
            ]
        );
        $response->assertStatus(201);
    }
}
