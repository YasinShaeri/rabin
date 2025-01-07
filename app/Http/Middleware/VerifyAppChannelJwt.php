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
                return $this->apiResponse(null, 'Invalid app_channel_id', false, 401);
            }

            // بررسی تطابق secret_key
            if ($appChannel->secret_key !== $secret_key) {
                return $this->apiResponse(null, 'Invalid secret key', false, 401);
            }

            // اضافه کردن app_channel_id به درخواست
            $request->merge(['app_channel_id' => $app_channel_id]);

            return $next($request);
        } catch (\Exception $e) {
            return $this->apiResponse(null, 'Unauthorized', false, 401);
        }
    }

    public function apiResponse($data = null, $message = null, $status = true, $statusCode = 200)
    {
        $response = [
            'success' => $status,
            'data' => $data,
            'message' => $message,
        ];

        return response()->json($response, $statusCode);
    }
}

