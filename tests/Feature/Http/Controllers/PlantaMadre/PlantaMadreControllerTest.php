<?php

namespace Tests\Feature\http\Controllers\PlantaMadre;

use App\Models\Propagacion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\Feature\Http\Controllers\Traits\AutenticacionTrait;
use Tests\TestCase;

class PlantaMadreControllerTest extends TestCase
{
    use RefreshDatabase;
    use AutenticacionTrait;

    /**
     * Test para probar rules de validación del método que busca lotes de propagación por un
     * rango de fechas.
     *
     * @return void
     */
    public function test_validacion_de_buscar_lotes_de_plantas_madres_por_rangos_de_fechas()
    {
        $this->withoutExceptionHandling();
        $token = $this->Autenticacion('cristian@gmail.com','admin123');
        $response = $this->get('/sajona/planta-madre/buscar-lotes?fecha_inicio=&fecha_fin=2021-10-04',
            [
                "Authorization" => "Bearer $token"
            ]
        );

        // $response->assertValid();
        $response->assertUnprocessable(); // si devuelve 422 pasa la prueba

    }

    /**
     * Test para probar que buscar registros correctamente en un rango de fechas.
     *
     * @return void
     */
    public function test_que_busca_registros_correctamente_de_lotes_de_plantas_madres_por_un_rango_fechas()
    {
        $this->withoutExceptionHandling();

        // Crando registros para pruebas.
        Propagacion::factory(5)->create();

        $token = $this->Autenticacion('cristian@gmail.com','admin123');

        // Creando registros temporales en memoria para realizar consulta.
        $response = $this->get('/sajona/planta-madre/buscar-lotes?fecha_inicio=2021-12-01&fecha_fin=2022-03-01',
            [
                "Authorization" => "Bearer $token"
            ]
        );

        // $response->assertValid();
        $response->assertStatus(200);
    }

    /**
     * Test para probar que buscar registros correctamente en un rango de fechas.
     *
     * @return void
     */
    public function test_para_visualizar_un_lote_en_especifico_correctamente()
    {
        $this->withoutExceptionHandling();

        $token = $this->Autenticacion('cristian@gmail.com','admin123');

        // Creamos un registro de propagación
        $propagacion = Propagacion::factory()->create();
        // Hacemos la solicitud a la url
        $response = $this->get('sajona/planta-madre/'.$propagacion->pro_id_lote,
            [
                "Authorization" => "Bearer $token"
            ]
        );

        // Validamos la respuesta
        $response->assertJsonStructure([
            'data'=> [
                "pro_id_lote",
                "pro_cantidad_plantas_madres",
                "pm_fecha_esquejacion",
                "pm_cantidad_esquejes",
                "pm_cantidad_semillas"
            ],
        ]);
        // $response->assertValid();
        $response->assertStatus(200);
    }

    /**
     * Test para probar validaciones al guardar esquejes semillas.
     *
     * @return void
     */
    public function test_de_validaciones_al_guardar_esquejes_semillas()
    {
        $this->withoutExceptionHandling();

        $token = $this->Autenticacion('cristian@gmail.com','admin123');

        // Registro temporal.
        Propagacion::create([
            "pro_id_lote"                   => 100,
            "pro_fecha"                     => date("Y-m-d H:i:s"),
            "pro_tipo_propagacion"          => "Esqueje",
            "pro_variedad"                  => rand(1,10),
            "pro_tipo_incorporacion"        => 'prueba',
            "pro_cantidad_material"         => 200,
            "pro_cantidad_plantas_madres"   => rand(1,50),
            "pro_estado"                    => true,
        ]);

        $response = $this->post('sajona/planta-madre',[
                'pm_pro_id_lote'                => 100,
                'pm_fecha_esquejacion'          => '2021-10-10ddd',
                'pm_cantidad_esquejes'          => "2s",
                'pm_cantidad_semillas'          => "s3"
            ],
            [
                "Authorization" => "Bearer $token"
            ]
        );

        // $response->assertValid();
        $response->assertUnprocessable(); // si devuelve 422 pasa la prueba
    }

    /**
     * Test para comprobar que se guardan esquejes semillas en planta madre correctamente.
     *
     * @return void
     */
    public function test_guardar_esquejes_semillas_correctamente()
    {
        $this->withoutExceptionHandling();

        $token = $this->Autenticacion('cristian@gmail.com','admin123');

        // Registro temporal.
        Propagacion::create([
            "pro_id_lote"                   => 100,
            "pro_fecha"                     => date("Y-m-d H:i:s"),
            "pro_tipo_propagacion"          => "Esqueje",
            "pro_variedad"                  => rand(1,10),
            "pro_tipo_incorporacion"        => 'prueba',
            "pro_cantidad_material"         => 200,
            "pro_cantidad_plantas_madres"   => rand(1,50),
            "pro_estado"                    => true,
        ]);

        $response = $this->post('sajona/planta-madre',[
                'pm_pro_id_lote'                => 100,
                'pm_fecha_esquejacion'          => '2021-10-10',
                'pm_cantidad_esquejes'          => 2,
                'pm_cantidad_semillas'          => 3
            ],
            [
                "Authorization" => "Bearer $token"
            ]
        );

        // $response->assertValid();
        $response->assertStatus(201);
    }
}
