<?php

namespace App\Repositories;

use App\Models\ProductsModel;

class ProductsRepository
{
    public function __construct()
    {
        $this->model = new ProductsModel();
    }
}