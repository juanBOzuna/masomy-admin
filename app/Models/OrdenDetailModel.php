<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrdenDetailModel extends Model
{
    use HasFactory;

    protected $table = 'orden_detail';


    protected $fillable = [
        'quantity',
        'subtotal',
        'disccount',
        'total',
        'product_id',
        'orden_id',
    ];
}
