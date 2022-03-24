<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CalendarEvent extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_title', 
        'event_start', 
        'event_end',
        'user_id',
        'heure_deb',
        'heure_fin'
    ];
}