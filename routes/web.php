<?php

use Illuminate\Support\Facades\Route;
use App\Http\Middleware\CheckSystem;

use App\Http\Livewire\Panel\{
    Dashboard\Index as DashboardIndex,
    Company\Index as CompanyIndex,
    User\Index as UserIndex,
};

use App\Http\Livewire\Auth\{
    Login,
    ForgotPassword,
    PasswordReset,
};

use App\Http\Controllers\{
    DocsController,
    EventsController,
    ReportController,
};

Route::get('/', function () {
    return redirect()->route('auth.login');
});

Route::group(['prefix' => 'auth', 'as' => 'auth.', 'middleware' => ['auth.redirect']], function () {
    Route::get('/login', Login::class)->name('login')->middleware(CheckSystem::class);
    Route::get('/forgot-password', ForgotPassword::class)->name('forgot.password');
    Route::get('/password-reset/{token}', PasswordReset::class)->name('password.reset');
});

Route::group(['prefix' => 'panel', 'as' => 'panel.', 'middleware' => ['auth.web']], function () {

    Route::group(['prefix' => 'dashboard', 'as' => 'dashboard.'], function () {
        Route::get('/', DashboardIndex::class)->name('index');
    });

    Route::group(['prefix' => 'companies', 'as' => 'companies.'], function () {
        Route::get('/', CompanyIndex::class)->name('index');
    });

    Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
        Route::get('/', UserIndex::class)->name('index');
    });

    Route::group(['prefix' => 'docs', 'as' => 'docs.'], function () {
        Route::get('/{id}/print-invoice', [DocsController::class, 'printInvoice'])->name('print.invoice')->middleware('access.invoice');
        Route::get('/{id}/print-event-nfenfce', [EventsController::class, 'printEvent_nfenfce'])->name('print.event.nfenfce');
        Route::get('/{id}/print-event-cte', [DocsController::class, 'printEvent_cte'])->name('print.event.cte');
    });

    Route::group(['prefix' => 'reports', 'as' => 'reports.'], function () {
        Route::get('/invoices', [ReportController::class, 'invoices'])->name('invoices');
        Route::get('/events', [ReportController::class, 'events'])->name('events');
        Route::get('/disables', [ReportController::class, 'disables'])->name('disables');
    });
});
