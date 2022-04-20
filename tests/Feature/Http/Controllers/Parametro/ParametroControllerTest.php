<?php

namespace Tests\Feature\Http\Controllers\Parametro;

use App\Models\parametros\TipoPropagacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ParametroControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_guarda_un_registro_de_una_tabla_parametro() {
        $this->withoutExceptionHandling();

        // Hacemos la solicitud a la url
        $response = $this->post('sajona/parametro/', [
                "descripcion"      => 'Nuevo valor',
                "estado"           => 'ACTIVO',
                "parametrica"      => 'pr_tipo_propagacion' // nombre de la tabla parametrica en la cual se va a realizar la acción
            ]
        );

        $response->assertStatus(201);
    }

    public function test_muestra_un_parametro_correctamente() {
        $this->withoutExceptionHandling();
        // Creamos un registro de Tipo de propagacion
        $tipoPropagacion = TipoPropagacion::factory(1)->create()->first();

        // Hacemos la solicitud a la url
        $response = $this->get('sajona/parametro/'.$tipoPropagacion->id);
        $response->assertJsonStructure([
            'data'=> [
                '*' => [
                    "id",
                    "descripcion",
                    "estado"
                ]
            ],
        ]);

        $response->assertStatus(200);
    }

    public function test_edita_un_registro_de_una_tabla_parametro_correctamente() {
        $this->withoutExceptionHandling();
        // Creamos un registro de Tipo de propagacion
        $tipoPropagacion = TipoPropagacion::factory(1)->create()->first();

        // Hacemos la solicitud a la url
        $response = $this->put('sajona/parametro/'.$tipoPropagacion->id, [
                "descripcion"      => 'valor a actualizar',
                "estado"           => 'ACTIVO',
                "parametrica"      => 'pr_tipo_propagacion' // nombre de la tabla parametrica en la cual se va a realizar la acción
            ]
        );

        $response->assertStatus(201);
    }

    public function test_validar_datos_al_editar_un_registro_de_una_tabla_parametro() {
        $this->withoutExceptionHandling();
        // Creamos un registro de Tipo de propagacion
        $tipoPropagacion = TipoPropagacion::factory(1)->create()->first();

        // Hacemos la solicitud a la url
        $response = $this->put('sajona/parametro/'.$tipoPropagacion->id, [
                "descripcion"      => '',
                "estado"           => 'pendiente',
                "parametrica"      => 'pr_tipo_propagacion' // nombre de la tabla parametrica en la cual se va a realizar la acción
            ]
        );

        $response->assertUnprocessable(); // si devuelve 422 pasa la prueba
        // $response->assertValid();
    }

    public function test_validar_datos_al_guardar_un_registro_de_una_tabla_parametro() {
        $this->withoutExceptionHandling();

        // Hacemos la solicitud a la url
        $response = $this->post('sajona/parametro/', [
            "descripcion"      => '',
            "estado"           => 'INCORRECTO TIENE QUE SER ACTIVO O INACTIVO',
            "parametrica"      => 'pr_tipo_propagacion' // nombre de la tabla parametrica en la cual se va a realizar la acción
        ]
    );

        $response->assertUnprocessable(); // si devuelve 422 pasa la prueba
        // $response->assertValid();
    }
}
