<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Jogo;
use Illuminate\Support\Facades\DB;

class JogoController extends Controller
{
    public function index($data_inicio_campeonato)
    {
        $list = DB::select("CALL proc_view_0001('" . $data_inicio_campeonato . "')");
        return response()->json($list);
    }
}
