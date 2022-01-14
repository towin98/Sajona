<?php

namespace Tests\Feature\http\Controllers\Transplante;

use App\Models\PlantaMadre;
use App\Models\Propagacion;
use App\Models\Transplante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TransplanteControllerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_listar_registros_de_transplante_a_bolsa_por_un_rango_de_fechas_correctamente()
    {
        $this->withoutExceptionHandling();

        // Crando registros para pruebas.
        for ($i=0; $i < 5; $i++) {
            $propagacion = Propagacion::factory()->create();
            PlantaMadre::create([
                'pm_pro_id_lote'        => $propagacion->pro_id_lote,
                'pm_fecha_esquejacion'  => date('Y-m-d H:i:s'),
                'pm_cantidad_semillas'  => rand(0,20),
                'pm_cantidad_esquejes'  => rand(0,20),
                'pm_estado'             => true,
            ]);
        }

        // Creando registros temporales en memoria para realizar consulta.
        $response = $this->get('/sajona/transplante-bolsa/buscar?fecha_inicial=2021-12-01&fecha_final=2022-03-01');

        $response->assertJsonStructure([
            'current_page',
            'data'=> [
                '*' => [
                    'id_lote',
                    'fecha_propagacion',
                    'fecha_transplante',
                    'accion',
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

        // $response->assertValid();
        $response->assertStatus(200);
    }

        /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_validaciones_al_listar_registros_de_transplante_a_bolsa_por_un_rango_de_fechas()
    {
        $this->withoutExceptionHandling();

        // Creando registros temporales en memoria para realizar consulta.
        $response = $this->get('/sajona/transplante-bolsa/buscar?fecha_inicial=2021--12-01&fecha_final=2022--03-01');

        // $response->assertValid();
        $response->assertUnprocessable(); // si devuelve 422 pasa la prueba
    }

    /**
     * Test para visualizar un registro de la tabla de transplante.
     *
     * @return void
     */
    public function test_para_visualizar_un_transplante_a_bolsa_en_especifico_correctamente()
    {
        $this->withoutExceptionHandling();

        // Crando registros para pruebas.
        $propagacion = Propagacion::create([
            "pro_id_lote"                   => 100,
            "pro_fecha"                     => date("Y-m-d H:i:s"),
            "pro_tipo_propagacion"          => "Esqueje",
            "pro_variedad"                  => rand(1,10),
            "pro_tipo_incorporacion"        => 'prueba',
            "pro_cantidad_material"         => 200,
            "pro_cantidad_plantas_madres"   => rand(1,50),
            "pro_estado"                    => true,
        ]);
        PlantaMadre::create([
            'pm_id'                 => 10,
            'pm_pro_id_lote'        => $propagacion->pro_id_lote,
            'pm_fecha_esquejacion'  => date('Y-m-d H:i:s'),
            'pm_cantidad_semillas'  => rand(0,20),
            'pm_cantidad_esquejes'  => rand(0,20),
            'pm_estado'             => true,
        ]);

        // Crando registro para prueba.
        Transplante::create([
            'tp_pm_id'          => 10,
            'tp_tipo'           => 'transplante_bolsa',
            'tp_tipo_lote'      => 'Planta Madre',
            'tp_fecha'          => '2022-01-14',
            'tp_ubicacion'      => 'Casa malla',
            'tp_cantidad_area'  => 32,
            'tp_estado'         => true,
        ]);

        // Creando registros temporales en memoria para realizar consulta.
        $response = $this->get('sajona/transplante-bolsa/100');

        // $response->assertValid();
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data'=> [
                '*' => [
                    'id_lote',
                    'tp_fecha',
                    'cantidad_buenas',
                    'tp_ubicacion',
                    'tp_cantidad_area',
                ]
            ]
        ]);
    }

    /**
     * Validación al visualizar un transplante, en este caso el id del transplante a bolsa o campo no
     * existe en base de datos.
     *
     * @return void
     */
    public function test_de_validaciones_al_visualizar_un_transplante()
    {
        // Creando registros temporales en memoria para realizar consulta.
        $response = $this->get('sajona/transplante-bolsa/100u2338273');
        // El transplante no existe en base de datos.
        $response->assertStatus(404);
    }

    /**
     * Test de validaciones al guardar transplante a bolsa o campo.
     *
     * @return void
     */
    public function test_de_validaciones_al_guardar_transplante_a_bolsa_o_campo()
    {
        $this->withoutExceptionHandling();

        $response = $this->post('sajona/transplante-bolsa',[
        ]);

        $response->assertUnprocessable(); // si devuelve 422 pasa la prueba
    }

    /**
     * Test para comprobar que se guarda correctamente transplante a bolsa o campo.
     *
     * @return void
     */
    public function test_para_comprobar_que_se_guarda_transplante_bolsa_o_campo_correctamente()
    {
        $this->withoutExceptionHandling();

        // Crando registros para pruebas.
        $propagacion = Propagacion::create([
            "pro_id_lote"                   => 100,
            "pro_fecha"                     => date("Y-m-d H:i:s"),
            "pro_tipo_propagacion"          => "Esqueje",
            "pro_variedad"                  => rand(1,10),
            "pro_tipo_incorporacion"        => 'prueba',
            "pro_cantidad_material"         => 200,
            "pro_cantidad_plantas_madres"   => rand(1,50),
            "pro_estado"                    => true,
        ]);
        PlantaMadre::create([
            'pm_id'                 => 10,
            'pm_pro_id_lote'        => $propagacion->pro_id_lote,
            'pm_fecha_esquejacion'  => date('Y-m-d H:i:s'),
            'pm_cantidad_semillas'  => rand(0,20),
            'pm_cantidad_esquejes'  => rand(0,20),
            'pm_estado'             => true,
        ]);

        $response = $this->post('sajona/transplante-bolsa',[
            'tp_pm_id'          => 100,
            'tp_tipo'           => 'transplante_bolsa',
            'tp_tipo_lote'      => 'Planta Madre',
            'tp_fecha'          => '2022-01-14',
            'tp_ubicacion'      => 'Casa malla',
            'tp_cantidad_area'  => 32,
            'tp_estado'         => true,
        ]);

        // $response->assertValid();
        $response->assertStatus(201);
    }
}
