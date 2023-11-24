<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketsModel extends Model
{

    const LEDIO = "leido";
    const PENDIENTE = "pendiente";
    use HasFactory;
    protected $table = 'tickets';

    protected $fillable = [
        'subject',
        'message',
        'status',
        'user_id'
    ];
}
