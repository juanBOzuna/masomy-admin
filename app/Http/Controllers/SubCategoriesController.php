<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SubCategoriesModel;

class SubCategoriesController extends Controller
{
    /**
     * Display a listing of the subcategories with their products.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $subcategoriesWithProducts = SubCategoriesModel::with('products')->get();
        return response()->json($subcategoriesWithProducts, 200);
    }

    /**
     * Display the specified subcategory with its products.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subcategoryWithProducts = SubCategoriesModel::with('products')->find($id);

        if (!$subcategoryWithProducts) {
            return response()->json(['error' => 'Subcategory not found'], 404);
        }

        return response()->json($subcategoryWithProducts, 200);
    }
}
