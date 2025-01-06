<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttachmentMessage extends Model
{
    use HasFactory;

    protected $table = "attachments_messages";

    protected $fillable = [
        'message_id',
        'file_id',
    ];

    public function message()
    {
        return $this->belongsTo(Message::class);
    }

    public function file()
    {
        return $this->belongsTo(File::class);
    }
}
