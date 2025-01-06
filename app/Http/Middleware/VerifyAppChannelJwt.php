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
            // استخراج توکن و اعتبارسنجی
            $decoded = JWTAuth::parseToken()->getPayload();

            // پیدا کردن AppChannel مرتبط با `sub` (subject)
            $appChannel = AppChannel::find($decoded->get('sub'));

            if (!$appChannel || $appChannel->jwt !== JWTAuth::getToken() || now()->greaterThan($appChannel->expire_time)) {
                throw new HttpResponseException(response()->json(['error' => 'Token is invalid or expired'], 401));
            }

            // ذخیره اطلاعات app_channel در request
            $request->merge(['app_channel' => $appChannel]);
        } catch (\Exception $e) {
            throw new HttpResponseException(response()->json(['error' => 'Token is invalid'], 401));
        }

        return $next($request);
    }
}
