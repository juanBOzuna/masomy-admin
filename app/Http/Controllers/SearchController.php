<?php

namespace App\Http\Controllers;

use App\Models\ProductsModel;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    //

    public function search(Request $request)
    {
        $searchQuery = $request->input('search_query');
        $searchTerms = explode(' ', $searchQuery);
        $results = $this->performSearch($searchTerms);
        return response()->json($results);
    }


    private function performSearch($searchTerms, $perPage = 9)
    {
        // Inicializa la consulta
        $query = ProductsModel::query();
        $query->where(function ($q) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $q->orWhere('name', 'like', "%{$term}%");
            }
        });
        $query->orWhere(function ($q) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $q->orWhere('description', 'like', "%{$term}%");
            }
        });
        $query->orWhere(function ($q) use ($searchTerms) {
            foreach ($searchTerms as $term) {
                $q->orWhere('price', 'like', "%{$term}%");
            }
        });
        $results = $query->distinct()->paginate($perPage);

        return $results;
    }

}
