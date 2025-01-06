<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use HasFactory;

    protected $table = "tickets";

    protected $fillable = [
        'client_id',
        'ticket_status_id',
        'priority_id',
        'app_channel_id',
        'app_channel_category_id',
        'title',
    ];

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function ticketStatus()
    {
        return $this->belongsTo(TicketStatus::class);
    }

    public function priority()
    {
        return $this->belongsTo(Priority::class);
    }

    public function appChannel()
    {
        return $this->belongsTo(AppChannel::class);
    }

    public function appChannelCategory()
    {
        return $this->belongsTo(AppChannelCategory::class);
    }

    public function messages()
    {
        return $this->hasMany(Message::class);
    }
}
