<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckSystem;

use App\Http\Controllers\{
    DocsController,
    EventsController,
};

Route::group(['prefix' => 'docs', 'as' => 'docs.'], function () {
    Route::post('/nfenfce/upload', [DocsController::class, 'nfe_nfce'])->name('nfe_nfce')->middleware(CheckSystem::class);
	Route::post('/nfe/upload', [DocsController::class, 'nfe'])->name('nfe')->middleware(CheckSystem::class);
    Route::post('/sat/upload', [DocsController::class, 'sat'])->name('sat')->middleware(CheckSystem::class);
    Route::post('/cte/upload', [DocsController::class, 'cte'])->name('cte')->middleware(CheckSystem::class);
    Route::post('/mdfe/upload', [DocsController::class, 'mdfe'])->name('mdfe')->middleware(CheckSystem::class);
    Route::post('/eventos/nfenfce/upload', [EventsController::class, 'cancelamento_cce'])->name('cancelamento_cce')->middleware(CheckSystem::class);
    Route::post('/eventos/cte/upload', [EventsController::class, 'cancelamento_cce_cte'])->name('cancelamento_cce_cte')->middleware(CheckSystem::class);
    Route::post('/inutilizacao/nfenfce/upload', [EventsController::class, 'inutilizacao_nfenfce'])->name('inutilizacao_nfenfce')->middleware(CheckSystem::class);
    Route::post('/inutilizacao/cte/upload', [EventsController::class, 'inutilizacao_cte'])->name('inutilizacao_cte')->middleware(CheckSystem::class);
    Route::get('/imprimir', [DocsController::class, 'imprimir'])->name('imprimir')->middleware(CheckSystem::class);
});
