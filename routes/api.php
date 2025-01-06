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
    $validated = $request->validate([
        'app_channel_id' => 'required|exists:app_channel,id',
        'secret_key' => 'required',
    ]);

    $appChannel = AppChannel::find($validated['app_channel_id']);

    // بررسی secret_key
    if ($appChannel->jwt !== $validated['secret_key']) {
        return response()->json(['error' => 'Invalid credentials'], 401);
    }

    // ساخت توکن JWT
    $payload = [
        'sub' => $appChannel->id, // subject
        'iat' => now()->timestamp, // زمان صدور
        'exp' => now()->addHours(1)->timestamp, // زمان انقضا
    ];

    // استفاده از JWTAuth فاساد برای ساخت توکن
    $token = \Tymon\JWTAuth\Facades\JWTAuth::class::claims($payload)->fromUser($appChannel);

    // ذخیره توکن در مدل AppChannel
    /*$appChannel->update([
        'jwt' => $token,
        'expire_time' => now()->addHours(1), // زمان انقضا
    ]);*/

    return response()->json([
        'success' => true,
        'token' => $token,
        /*'expires_at' => $appChannel->expire_time,*/
    ]);
});


Route::group(['middleware' => ['verify.app_channel.jwt']], function () {
    Route::prefix('ticket')->group(function () {
        Route::post('/create', [TicketController::class, 'store'])->name('ticket.create');
        Route::post('/getTickets', [TicketController::class, 'getTickets'])->name('ticket.getTickets');
        Route::post('/message/create', [MessageController::class, 'addMessage'])->name('ticket.message.create');
    });
});
