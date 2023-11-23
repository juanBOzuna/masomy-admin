<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreOrdenDetailModel extends Model
{
    use HasFactory;
    protected $table = 'pre_orden_detail';


    protected $fillable = [
        'quantity',
        'subtotal',
        'disccount',
        'total',
        'product_id',
        'pre_orden_id',
    ];


}
