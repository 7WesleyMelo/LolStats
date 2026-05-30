<?php

use App\Http\Controllers\CampeaoController;
use App\Http\Controllers\FeiticoInvocadorController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\PatchController;
use App\Http\Controllers\RunaController;
use Illuminate\Support\Facades\Route;
use App\Services\Riot\DataDragonService;

Route::get('/riot/versao-atual', function (DataDragonService $dataDragon) {
    return response()->json([
        'versao_atual' => $dataDragon->latestVersion(),
        'idioma' => config('services.riot.locale'),
    ]);
});

Route::get('/riot/campeoes', function (DataDragonService $dataDragon) {
    $dados = $dataDragon->champions();

    return response()->json([
        'versao' => $dados['version'],
        'idioma' => config('services.riot.locale'),
        'campeoes' => array_values($dados['data']),
    ]);
});

Route::get('/campeoes', [CampeaoController::class, 'index']);
Route::get('/campeoes/{riotId}', [CampeaoController::class, 'show']);

Route::get('/itens', [ItemController::class, 'index']);
Route::get('/itens/{riotId}', [ItemController::class, 'show']);

Route::get('/feiticos', [FeiticoInvocadorController::class, 'index']);

Route::get('/runas', [RunaController::class, 'index']);
Route::get('/runas/{riotId}', [RunaController::class, 'show']);
Route::get('/arvores-runas', [RunaController::class, 'index']);

Route::get('/patches', [PatchController::class, 'index']);
Route::get('/partidas', [MatchController::class, 'index']);
Route::get('/partidas/{riotMatchId}', [MatchController::class, 'show']);
