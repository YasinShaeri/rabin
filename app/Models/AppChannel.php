<?php

namespace App\Models;

use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Tymon\JWTAuth\Contracts\JWTSubject;

class AppChannel extends Model implements JWTSubject
{
    use HasFactory;

    protected $table = "app_channel";

    protected $fillable = [
        'app_id',
        'channel_id',
        'jwt',
        'expire_time'
    ];

    public function app()
    {
        return $this->belongsTo(App::class);
    }

    public function channel()
    {
        return $this->belongsTo(Channel::class);
    }

    public function categories()
    {
        return $this->hasMany(AppChannelCategory::class);
    }

    public function generateJwt()
    {
        $key = config('app.jwt_secret'); // کلید JWT را از فایل .env بخوانید
        $payload = [
            'iss' => 'your-app-name', // نام برنامه شما
            'sub' => $this->id, // شناسه app_channel
            'iat' => now()->timestamp, // زمان ایجاد توکن
            'exp' => now()->addHours(2)->timestamp // زمان انقضا (مثلاً 2 ساعت)
        ];

        $token = JWT::encode($payload, $key, 'HS256'); // تولید توکن
        $this->update([
            'jwt' => $token,
            'expire_time' => Carbon::createFromTimestamp($payload['exp'])
        ]);

        return $token;
    }

    public function getJWTIdentifier()
    {
        return $this->getKey(); // شناسه‌ای که برای توکن استفاده می‌شود
    }

    public function getJWTCustomClaims()
    {
        return []; // اگر نیاز به ادعای سفارشی دارید، آن را اینجا اضافه کنید
    }
}
