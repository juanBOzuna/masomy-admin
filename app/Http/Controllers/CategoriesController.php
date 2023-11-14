<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CategoriesModel;

class CategoriesController extends Controller
{
    /**
     * Display a listing of the categories with their subcategories.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $categoriesWithSubcategories = CategoriesModel::with('subcategories')->get();
        return response()->json($categoriesWithSubcategories, 200);
    }

    /**
     * Display the specified category with its subcategories.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $categoryWithSubcategories = CategoriesModel::with('subcategories')->find($id);

        if (!$categoryWithSubcategories) {
            return response()->json(['error' => 'Category not found'], 404);
        }

        return response()->json($categoryWithSubcategories, 200);
    }
}
