<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\MineController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return redirect()->route('dashboard');
})->middleware('auth');

// احراز هویت
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('login', [AuthController::class, 'login'])->name('login.post');
Route::get('logout', [AuthController::class, 'logout'])->name('logout');

// داشبورد
Route::prefix('dashboard')->middleware('auth')->group(function () {
    Route::get('/', [MineController::class, 'dashboard'])->name('dashboard');
    Route::get('/ticket/list', [MineController::class, 'ticket'])->name('ticket.list');
    Route::get('/ticket/{ticket}', [MineController::class, 'ticketShow'])->name('ticket.show');
    Route::post('/ticket/{ticket}/message/create', [MineController::class, 'addMessage'])->name('ticket.message.create');
});

