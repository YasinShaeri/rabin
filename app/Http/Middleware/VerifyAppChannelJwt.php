<?php

namespace App\Http\Middleware;

use App\Models\AppChannel;
use Closure;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\Request;

class VerifyAppChannelJwt
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        try {
            // بررسی توکن JWT
            $user = JWTAuth::parseToken()->authenticate();

            // دریافت payload از توکن
            $tokenPayload = JWTAuth::parseToken()->getPayload();

            // استخراج app_channel_id و secret_key از payload توکن
            $app_channel_id = $tokenPayload->get('app_channel_id');
            $secret_key = $tokenPayload->get('secret_key');

            // بررسی موجود بودن appChannel با app_channel_id
            $appChannel = AppChannel::find($app_channel_id);

            if (!$appChannel) {
                return response()->json(['error' => 'Invalid app_channel_id'], 401);
            }

            // بررسی تطابق secret_key
            if ($appChannel->secret_key !== $secret_key) {
                return response()->json(['error' => 'Invalid secret key'], 401);
            }

            return $next($request);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }
    }
}

