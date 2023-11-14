<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValorationsModel extends Model
{
    use HasFactory;
    protected $table = 'valorations';

    public function product()
    {
        return $this->belongsTo(ProductsModel::class, 'product_id');
    }
}
