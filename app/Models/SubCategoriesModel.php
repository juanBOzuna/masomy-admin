<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubCategoriesModel extends Model
{
    use HasFactory;
    protected $table = "subcategories";

    public function products()
    {
        return $this->hasMany(ProductsModel::class, 'sub_categorie_id');
    }
}
