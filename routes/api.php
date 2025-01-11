<?php

use App\Http\Controllers\MessageController;
use App\Http\Controllers\TicketController;
use App\Http\Controllers\TokenController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::post('/generate-token', [TokenController::class, 'generateToken']);

Route::group(['middleware' => ['verify.app_channel.jwt']], function () {
    Route::prefix('ticket')->group(function () {
        Route::post('/create', [TicketController::class, 'store'])->name('ticket.create');
        Route::post('/lists', [TicketController::class, 'lists'])->name('ticket.lists');
        Route::post('/details', [TicketController::class, 'details'])->name('ticket.details');
        Route::post('/message/create', [MessageController::class, 'addMessage'])->name('ticket.message.create');
    });
});
