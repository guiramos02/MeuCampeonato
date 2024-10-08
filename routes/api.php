<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::apiResource('/times', 'App\Http\Controllers\TimeController');
Route::post('/iniciar-campeonato', 'App\Http\Controllers\CampeonatoController@iniciarCampeonato');

//Assumindo data_campeonato formato: YYYY-MM-DD HH:ii:ss
Route::get('/recuperar_campeonato/{data_campeonato}', 'App\Http\Controllers\JogoController@index');