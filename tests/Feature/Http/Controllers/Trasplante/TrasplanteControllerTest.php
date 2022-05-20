<?php

namespace Tests\Feature\http\Controllers\Trasplante;

use App\Models\PlantaMadre;
use App\Models\Propagacion;
use App\Models\Trasplante;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Http\Controllers\Traits\AutenticacionTrait;
use Tests\TestCase;

class TrasplanteControllerTest extends TestCase
{
    use RefreshDatabase;
    use AutenticacionTrait;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function test_listar_registros_de_trasplante_a_bolsa_por_un_rango_de_fechas_correctamente()
    {
        $this->withoutExceptionHandling();

        $token = $this->Autenticacion('cristian@gmail.com','admin123');

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
        $response = $this->get('/sajona/trasplante-bolsa/buscar?fecha_inicial=2021-12-01&fecha_final=2022-03-01',
            [
                "Authorization" => "Bearer $token"
            ]
        );

        $response->assertJsonStructure([
            'data'=> [
                '*' => [
                    'pm_id',
                    'id_lote',
                    'fecha_propagacion',
                    'pm_fecha_esquejacion',
                    'tp_fecha',
                    'accion',
                    'estado_lote',
                    'dias_transcurridos',
                    'color'
                ]
            ],
            'filtrados',
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
    public function test_validaciones_al_listar_registros_de_trasplante_a_bolsa_por_un_rango_de_fechas()
    {
        $this->withoutExceptionHandling();

        $token = $this->Autenticacion('cristian@gmail.com','admin123');

        // Creando registros temporales en memoria para realizar consulta.
        $response = $this->get('/sajona/trasplante-bolsa/buscar?fecha_inicial=2021--12-01&fecha_final=2022--03-01',
            [
                "Authorization" => "Bearer $token"
            ]
        );

        // $response->assertValid();
        $response->assertUnprocessable(); // si devuelve 422 pasa la prueba
    }

    /**
     * Test para visualizar un registro de la tabla de trasplante.
     *
     * @return void
     */
    public function test_para_visualizar_un_trasplante_a_bolsa_en_especifico_correctamente()
    {
        $this->withoutExceptionHandling();

        $token = $this->Autenticacion('cristian@gmail.com','admin123');

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
        Trasplante::create([
            'tp_pm_id'          => 10,
            'tp_tipo'           => 'trasplante_bolsa',
            'tp_tipo_lote'      => 'Planta Madre',
            'tp_fecha'          => '2022-01-14',
            'tp_ubicacion'      => 'Casa malla',
            'tp_cantidad_area'  => 32,
            'tp_estado'         => true,
        ]);

        // Creando registros temporales en memoria para realizar consulta.
        $response = $this->get('sajona/trasplante-bolsa/10',
            [
                "Authorization" => "Bearer $token"
            ]
        );

        // $response->assertValid();
        $response->assertStatus(200);

        $response->assertJsonStructure([
            'data'=> [
                'tp_pm_id',
                'tp_fecha',
                'cantidad_trasplante_bolsa',
                'tp_tipo_lote',
                'tp_ubicacion',
                'tp_cantidad_area'
            ]
        ]);
    }

    /**
     * Validación al visualizar un trasplante, en este caso el id del trasplante a bolsa o campo no
     * existe en base de datos.
     *
     * @return void
     */
    public function test_de_validaciones_al_visualizar_un_trasplante()
    {
        $token = $this->Autenticacion('cristian@gmail.com','admin123');

        // Creando registros temporales en memoria para realizar consulta.
        $response = $this->get('sajona/trasplante-bolsa/100u2338273', [
                "Authorization" => "Bearer $token"
            ]
        );
        // El trasplante no existe en base de datos.
        $response->assertStatus(404);
    }

    /**
     * Test de validaciones al guardar trasplante a bolsa o campo.
     *
     * @return void
     */
    public function test_de_validaciones_al_guardar_trasplante_a_bolsa_o_campo()
    {
        $this->withoutExceptionHandling();

        $token = $this->Autenticacion('cristian@gmail.com','admin123');

        $response = $this->post('sajona/trasplante-bolsa',[],
            [
                "Authorization" => "Bearer $token"
            ]
        );

        $response->assertUnprocessable(); // si devuelve 422 pasa la prueba
    }

    /**
     * Test para comprobar que se guarda correctamente trasplante a bolsa o campo.
     *
     * @return void
     */
    public function test_para_comprobar_que_se_guarda_trasplante_bolsa_o_campo_correctamente()
    {
        $this->withoutExceptionHandling();

        $token = $this->Autenticacion('cristian@gmail.com','admin123');

        // Crando registros para pruebas.
        $propagacion = Propagacion::create([
            "pro_id_lote"                   => 100,
            "pro_fecha"                     => date("Y-m-d H:i:s"),
            "pro_tipo_propagacion"          => 1,
            "pro_variedad"                  => 1,
            "pro_tipo_incorporacion"        => 1,
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

        $response = $this->post('sajona/trasplante-bolsa',[
                'tp_pm_id'          => 10,
                'tp_tipo'           => 'trasplante_bolsa',
                'tp_tipo_lote'      => 'bolsa',
                'tp_fecha'          => '2022-01-14',
                'tp_ubicacion'      => 'Casa malla',
                'tp_cantidad_area'  => 32,
                'tp_estado'         => true,
            ],
            [
                "Authorization" => "Bearer $token"
            ]
        );

        // $response->assertValid();
        $response->assertStatus(201);
    }
}
