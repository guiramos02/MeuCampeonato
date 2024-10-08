<?php

namespace App\Http\Controllers;

use App\Models\Jogo;
use App\Models\Time;
use Illuminate\Http\Request;

class CampeonatoController extends Controller
{
    public function iniciarCampeonato(Request $request)
    {
        $dataInicioCampeonato = now();
        $nomesTimes = $request->input('times');

        $times = Time::whereIn('nome', $nomesTimes)->get();

        $timesNaoEncontrados = array_diff($nomesTimes, $times->pluck('nome')->toArray());

        if (count($timesNaoEncontrados) > 0) {
            return response()->json([
                'error' => 'Os seguintes times não foram encontrados: ' . implode(', ', $timesNaoEncontrados)
            ], 404);
        }

        if ($times->count() != 8) {
            return response()->json(['error' => 'Deve haver exatamente 8 times.'], 400);
        }

        $timesEmbaralhados = $times->shuffle();

        $jogos = [];
        for ($i = 0; $i < count($timesEmbaralhados); $i += 2) {
            $jogo = Jogo::create([
                'time_casa' => $timesEmbaralhados[$i]->id,
                'time_fora' => $timesEmbaralhados[$i + 1]->id,
                'fase' => 'quartas_final',
                'data_inicio_campeonato' => $dataInicioCampeonato,
            ]);

            $gols_time_casa = rand(0, 5);
            $gols_time_fora = rand(0, 5);

            $jogo->update([
                'gols_time_casa' => $gols_time_casa,
                'gols_time_fora' => $gols_time_fora,
            ]);

            $jogos[] = $jogo;
        }

        $vencedoresQuartas = $this->determinarVencedores($jogos);

        $this->calcularPontuacao($jogos);

        $resultadosQuartas = [];
        foreach ($jogos as $jogo) {
            $resultadosQuartas[] = [
                'time_casa' => $jogo->timeCasa->nome,
                'gols_time_casa' => $jogo->gols_time_casa,
                'time_fora' => $jogo->timeFora->nome,
                'gols_time_fora' => $jogo->gols_time_fora,
            ];
        }

        shuffle($vencedoresQuartas);

        $jogosSemis = [];
        for ($i = 0; $i < count($vencedoresQuartas); $i += 2) {
            if (isset($vencedoresQuartas[$i + 1])) {
                $jogo = Jogo::create([
                    'time_casa' => $vencedoresQuartas[$i]->id,
                    'time_fora' => $vencedoresQuartas[$i + 1]->id,
                    'fase' => 'semi_final',
                    'data_inicio_campeonato' => $dataInicioCampeonato,
                ]);

                $gols_time_casa = rand(0, 5);
                $gols_time_fora = rand(0, 5);

                $jogo->update([
                    'gols_time_casa' => $gols_time_casa,
                    'gols_time_fora' => $gols_time_fora,
                ]);

                $jogosSemis[] = $jogo;
            }
        }

        $vencedoresSemi = $this->determinarVencedores($jogosSemis);

        $perdedoresSemi = $this->determinarPerdedores($jogosSemis);

        $this->calcularPontuacao($jogosSemis);

        $resultadosSemi = [];
        foreach ($jogosSemis as $jogo) {
            $resultadosSemi[] = [
                'time_casa' => $jogo->timeCasa->nome,
                'gols_time_casa' => $jogo->gols_time_casa,
                'time_fora' => $jogo->timeFora->nome,
                'gols_time_fora' => $jogo->gols_time_fora,
            ];
        }

        $jogoFinal = [];
        if (count($vencedoresSemi) == 2) {
            $jogo = Jogo::create([
                'time_casa' => $vencedoresSemi[0]->id,
                'time_fora' => $vencedoresSemi[1]->id,
                'fase' => 'final',
                'data_inicio_campeonato' => $dataInicioCampeonato,
            ]);

            $gols_time_casa = rand(0, 5);
            $gols_time_fora = rand(0, 5);

            $jogo->update([
                'gols_time_casa' => $gols_time_casa,
                'gols_time_fora' => $gols_time_fora,
            ]);

            $jogoFinal[] = $jogo;
        }

        $vencedorFinal = $this->determinarVencedores($jogoFinal);

        $this->calcularPontuacao($jogoFinal);

        $resultadosFinal = [];
        foreach ($jogoFinal as $jogo) {
            $resultadosFinal[] = [
                'time_casa' => $jogo->timeCasa->nome,
                'gols_time_casa' => $jogo->gols_time_casa,
                'time_fora' => $jogo->timeFora->nome,
                'gols_time_fora' => $jogo->gols_time_fora,
            ];
        }

        $jogoTerceiroLugar = [];
        if (count($perdedoresSemi) == 2) {
            $jogo = Jogo::create([
                'time_casa' => $perdedoresSemi[0]->id,
                'time_fora' => $perdedoresSemi[1]->id,
                'fase' => 'terceiro_lugar',
                'data_inicio_campeonato' => $dataInicioCampeonato,

            ]);

            $gols_time_casa = rand(0, 5);
            $gols_time_fora = rand(0, 5);

            $jogo->update([
                'gols_time_casa' => $gols_time_casa,
                'gols_time_fora' => $gols_time_fora,
            ]);

            $jogoTerceiroLugar[] = $jogo;
        }

        $vencedorTerceiroLugar = $this->determinarVencedores($jogoTerceiroLugar);

        $resultadosTerceiroLugar = [];
        foreach ($jogoTerceiroLugar as $jogo) {
            $resultadosTerceiroLugar[] = [
                'time_casa' => $jogo->timeCasa->nome,
                'gols_time_casa' => $jogo->gols_time_casa,
                'time_fora' => $jogo->timeFora->nome,
                'gols_time_fora' => $jogo->gols_time_fora,
            ];
        }

        return response()->json([
            'quartas' => $resultadosQuartas,
            'semifinais' => $resultadosSemi,
            'final' => $resultadosFinal,
            'terceiro_lugar' => $resultadosTerceiroLugar,
            'vencedor_final' => $vencedorFinal,
            'vencedor_terceiro_lugar' => $vencedorTerceiroLugar,
            'pontuacoes' => $this->obterPontuacoes(),
        ]);
    }

    private function determinarPerdedores($jogos)
    {
        $perdedores = [];

        foreach ($jogos as $jogo) {
            if ($jogo->gols_time_casa < $jogo->gols_time_fora) {
                $perdedores[] = $jogo->timeCasa;
            } elseif ($jogo->gols_time_fora < $jogo->gols_time_casa) {
                $perdedores[] = $jogo->timeFora;
            } else {
                $perdedores[] = $jogo->timeFora;
            }
        }

        return $perdedores;
    }

    private function determinarVencedores($jogos)
    {
        $pontuacoes = $this->obterPontuacoes();

        $vencedores = [];

        foreach ($jogos as $jogo) {
            if ($jogo->gols_time_casa > $jogo->gols_time_fora) {
                $vencedores[] = $jogo->timeCasa;
            } elseif ($jogo->gols_time_fora > $jogo->gols_time_casa) {
                $vencedores[] = $jogo->timeFora;
            } else {
                $pontuacaoTimeCasa = $pontuacoes[$jogo->timeCasa->nome] ?? 0;
                $pontuacaoTimeFora = $pontuacoes[$jogo->timeFora->nome] ?? 0;

                $pontuacaoTimeCasa += $jogo->gols_time_casa;
                $pontuacaoTimeCasa -= $jogo->gols_time_fora;

                $pontuacaoTimeFora += $jogo->gols_time_fora;
                $pontuacaoTimeFora -= $jogo->gols_time_casa;

                if ($pontuacaoTimeCasa > $pontuacaoTimeFora) {
                    $vencedores[] = $jogo->timeCasa;
                } elseif ($pontuacaoTimeFora > $pontuacaoTimeCasa) {
                    $vencedores[] = $jogo->timeFora;
                } else {
                    $dataInscricaoTimeCasa = $jogo->timeCasa->data_inscricao;
                    $dataInscricaoTimeFora = $jogo->timeFora->data_inscricao;

                    if ($dataInscricaoTimeCasa < $dataInscricaoTimeFora) {
                        $vencedores[] = $jogo->timeCasa;
                    } elseif ($dataInscricaoTimeFora < $dataInscricaoTimeCasa) {
                        $vencedores[] = $jogo->timeFora;
                    } else {
                        $vencedores[] = $jogo->timeCasa;
                    }
                }
            }
        }

        return $vencedores;
    }

    private function calcularPontuacao($jogos)
    {
        $pontuacoes = [];

        foreach ($jogos as $jogo) {
            $timeCasa = $jogo->timeCasa;
            $timeFora = $jogo->timeFora;

            if (!isset($pontuacoes[$timeCasa->nome])) {
                $pontuacoes[$timeCasa->nome] = 0;
            }
            if (!isset($pontuacoes[$timeFora->nome])) {
                $pontuacoes[$timeFora->nome] = 0;
            }

            $pontuacoes[$timeCasa->nome] += $jogo->gols_time_casa;
            $pontuacoes[$timeCasa->nome] -= $jogo->gols_time_fora;

            $pontuacoes[$timeFora->nome] += $jogo->gols_time_fora;
            $pontuacoes[$timeFora->nome] -= $jogo->gols_time_casa;
        }

        arsort($pontuacoes);

        return $pontuacoes;
    }

    private function obterPontuacoes()
    {
        $jogos = Jogo::all();
        $pontuacoes = [];

        foreach ($jogos as $jogo) {
            if (!isset($pontuacoes[$jogo->timeCasa->nome])) {
                $pontuacoes[$jogo->timeCasa->nome] = 0;
            }
            $pontuacoes[$jogo->timeCasa->nome] += $jogo->gols_time_casa;
            $pontuacoes[$jogo->timeCasa->nome] -= $jogo->gols_time_fora;

            if (!isset($pontuacoes[$jogo->timeFora->nome])) {
                $pontuacoes[$jogo->timeFora->nome] = 0;
            }
            $pontuacoes[$jogo->timeFora->nome] += $jogo->gols_time_fora;
            $pontuacoes[$jogo->timeFora->nome] -= $jogo->gols_time_casa;
        }

        arsort($pontuacoes);
        return $pontuacoes;
    }
}
