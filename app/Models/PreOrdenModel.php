<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrdenModel extends Model
{
    use HasFactory;
    protected $table = 'pre_orden';

    protected $fillable = [
        'total_price',
        'total_disccount',
        'payment_link_id'
    ];
}
