<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductsModel extends Model
{
    use HasFactory;

    protected $table = "products";

    public function valorations()
    {
        return $this->hasMany(ValorationsModel::class, 'product_id');
    }
}
