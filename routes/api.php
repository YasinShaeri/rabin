<?php

use App\Http\Controllers\MessageController;
use App\Http\Controllers\TicketController;
use App\Models\AppChannel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Tymon\JWTAuth\JWTAuth;

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


Route::post('/generate-token', function (Request $request) {
    //dd("ok");
   // dd($request);
    $validated = $request->validate([
        'app_channel_id' => 'required',
        'secret_key' => 'required',
    ]);
//dd($validated );
    $appChannel = AppChannel::find($validated['app_channel_id']);

    if (! $appChannel) {
        return response()->json(['error' => 'Invalid app_channel_id'], 401);
    }


    // بررسی secret_key
    if ($appChannel->secret_key !== $validated['secret_key']) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    // ساخت توکن JWT
    $payload = [
        'sub' => $appChannel->id, // subject
        'iat' => now()->timestamp, // زمان صدور
        'exp' => now()->addYear(1)->timestamp, // زمان انقضا
        'app_channel_id' => $appChannel->id, // اضافه کردن app_channel_id به claims
        'secret_key' => $appChannel->secret_key, // اضافه کردن secret_key به claims
    ];
    
    // استفاده از JWTAuth فاساد برای ساخت توکن
    $token = \Tymon\JWTAuth\Facades\JWTAuth::class::claims($payload)->fromUser($appChannel);

    return response()->json([
        'success' => true,
        'token' => $token,
        'expires_at' => $payload['exp'],
    ]);
});


Route::group(['middleware' => ['verify.app_channel.jwt']], function () {
    Route::prefix('ticket')->group(function () {
        Route::post('/create', [TicketController::class, 'store'])->name('ticket.create');
        Route::post('/getTickets', [TicketController::class, 'getTickets'])->name('ticket.getTickets');
        Route::post('/message/create', [MessageController::class, 'addMessage'])->name('ticket.message.create');
    });
});
