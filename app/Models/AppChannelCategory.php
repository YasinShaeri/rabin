<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppChannelCategory extends Model
{
    use HasFactory;

    protected $table = "app_channel_categories";

    protected $fillable = [
        'app_channel_id',
        'title',
    ];

    public function appChannel()
    {
        return $this->belongsTo(AppChannel::class);
    }
}
