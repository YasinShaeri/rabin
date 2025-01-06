<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class App extends Model
{
    use HasFactory;

    protected $table = "apps";

    protected $fillable = [
        'title',
        'username',
        'api_url_get_client'
    ];
}
