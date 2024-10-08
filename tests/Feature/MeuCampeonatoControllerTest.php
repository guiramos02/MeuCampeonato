<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class MeuCampeonatoControllerTest extends TestCase
{
    public function test_iniciar_campeonato_com_sucesso()
    {
        $times = [
            "Santos",
            "Corinthians",
            "Vasco",
            "Botafogo",
            "Bahia",
            "Vitória",
            "Cuiabá",
            "Brusque"
        ];

        $data = [
            'times' => $times,
        ];

        $response = $this->postJson('/api/iniciar-campeonato', $data);

        $response->assertStatus(200);

        $response->assertJsonStructure([
            'quartas',
            'semifinais',
            'final',
            'terceiro_lugar',
            'vencedor_final',
            'vencedor_terceiro_lugar',
            'pontuacoes'
        ]);
    }

    public function test_iniciar_campeonato_com_times_nao_encontrados()
    {
        $timesValidos = [
            "Santos",
            "Corinthians",
            "Vasco",
            "Botafogo",
        ];
        $nomesTimesInvalidos = [
            'Time Inexistente 1',
            'Time Inexistente 2',
            'Time Inexistente 3',
            'Time Inexistente 4',
        ];

        $data = [
            'times' => array_merge($timesValidos, $nomesTimesInvalidos),
        ];

        $response = $this->postJson('/api/iniciar-campeonato', $data);

        $response->assertStatus(404);

        $response->assertJson([
            'error' => 'Os seguintes times não foram encontrados: Time Inexistente 1, Time Inexistente 2, Time Inexistente 3, Time Inexistente 4'
        ]);
    }

    public function test_iniciar_campeonato_com_times_insuficientes()
    {
        $timesInsuficientes = [
            "Santos",
            "Corinthians"
        ];

        $data = [
            'times' => $timesInsuficientes,
        ];

        $response = $this->postJson('/api/iniciar-campeonato', $data);

        $response->assertStatus(400);
        $response->assertJson([
            'error' => 'Deve haver exatamente 8 times.'
        ]);
    }
}
