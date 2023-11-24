<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ValorationsModel extends Model
{
    use HasFactory;
    protected $table = 'valorations';

    protected $fillable = [
        'rating',
        'comment',
        'user_id',
        'product_id'
    ];

    public function product()
    {
        return $this->belongsTo(ProductsModel::class, 'product_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
