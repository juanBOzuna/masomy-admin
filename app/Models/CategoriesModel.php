<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CategoriesModel extends Model
{
    use HasFactory;
    protected $table = "categories";

    public function subcategories()
    {
        return $this->hasMany(SubCategoriesModel::class, 'categoria_id')->with('products');
    }
}
