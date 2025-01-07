<?php

namespace App\Http\Controllers;

use App\Models\AppChannel;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class TokenController extends Controller
{
    public function generateToken(Request $request)
    {
        $validated = $request->validate([
            'app_channel_id' => 'required',
            'secret_key' => 'required',
        ]);

        // جستجو برای app_channel
        $appChannel = AppChannel::find($validated['app_channel_id']);

        if (!$appChannel) {
            return response()->json(['error' => 'Invalid app_channel_id'], 401);
        }

        // بررسی تطابق secret_key
        if ($appChannel->secret_key !== $validated['secret_key']) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // ساخت توکن JWT
        $payload = [
            'sub' => $appChannel->id, // subject
            'iat' => now()->timestamp, // زمان صدور
            'exp' => now()->addYear(10)->timestamp, // زمان انقضا
            'app_channel_id' => $appChannel->id, // اضافه کردن app_channel_id به claims
            'secret_key' => $appChannel->secret_key, // اضافه کردن secret_key به claims
        ];

        // استفاده از JWTAuth فاساد برای ساخت توکن
        $token = JWTAuth::claims($payload)->fromUser($appChannel);

        // بازگشت توکن به همراه زمان انقضا
        return $this->apiResponse(
            ['token' => $token, 'expires_at' => $payload['exp']],
            'Token generated successfully',
            true
        );
    }
}
