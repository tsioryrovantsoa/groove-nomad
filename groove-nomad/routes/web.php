<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FestivalController;
use App\Http\Controllers\ProposalController;
use App\Http\Controllers\RequestController;

Route::get('/', fn() => view('home'))->name('home');

Route::get('/festival', [FestivalController::class, 'index'])->name('festival.index');

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'dologin'])->name('auth.login');

    Route::get('/register', [AuthController::class, 'register'])->name('auth.register');
    Route::post('/register', [AuthController::class, 'doregister'])->name('auth.register');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('auth.logout');

    Route::prefix('request')->name('request.')->group(function () {
        Route::get('/', [RequestController::class, 'index'])->name('index');
        Route::get('/create', [RequestController::class, 'create'])->name('create');
        Route::post('/step1', [RequestController::class, 'storeStep1'])->name('store.step1');
        Route::get('/create/step2', [RequestController::class, 'createStep2'])->name('create.step2');
        Route::post('/', [RequestController::class, 'store'])->name('store');
    });

    Route::prefix('chat')->name('chat.')->group(function () {
        Route::get('/', [RequestController::class, 'chat'])->name('index');
    });

    Route::put('/proposals/{proposal}/reject', [ProposalController::class, 'reject'])
        ->name('proposals.reject');

    Route::put('/proposals/{proposal}/accept', [ProposalController::class, 'acceptAndRedirectToStripe'])
        ->name('proposals.accept');

    Route::get('/proposals/{proposal}/payment/success', [ProposalController::class, 'handleStripeSuccess'])
        ->name('proposals.payment.success');
});

Route::get('/test-error', function () {
    throw new \Exception('Test d\'erreur Discord');
});