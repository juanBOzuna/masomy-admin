<?php

namespace App\Http\Controllers;

use App\Models\ProductsModel;
use App\Models\ValorationsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductsController extends Controller
{

    public function index()
    {
        $products = ProductsModel::all();
        return response()->json(['succes' => true]);
    }

    public function show($id)
    {

        $product = ProductsModel::with(['valorations.user:id,name'])
            ->find($id);
    
        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }
    

        $totalReviews = $product->valorations->count();
    

        $averageRating = $totalReviews > 0 ? $product->valorations->avg('rating') : 0;
    

        $product->total_reviews = $totalReviews;
        $product->average_rating = $averageRating;
    
        return response()->json($product);
    }

    public function store(Request $request)
    {
        $product = ProductsModel::create($request->all());
        return response()->json($product, 201);
    }

    public function update(Request $request, $id)
    {
        $product = ProductsModel::find($id);
        $product->update($request->all());
        return response()->json($product, 200);
    }

    public function destroy($id)
    {
        ProductsModel::destroy($id);
        return response()->json(null, 204);
    }

    public function getTopRatedProducts()
    {
        $topProducts = ProductsModel::with([
            'valorations' => function ($query) {
                $query->select('product_id', \DB::raw('COUNT(*) as total_reviews'), \DB::raw('SUM(rating) as total_rating'))
                    ->groupBy('product_id');
            }
        ])
            ->whereHas('valorations') // Asegura que solo se seleccionen productos con al menos una valoración
            ->get();


        $topProducts = $topProducts->sortByDesc(function ($product) {
            $totalReviews = $product->valorations->first()->total_reviews ?? 0;
            $totalRating = $product->valorations->first()->total_rating ?? 0;

            return $totalReviews > 0 ? $totalRating / $totalReviews : 0;
        });

        if (count($topProducts) < 3) {
            $topProducts2 = ProductsModel::with([
                'valorations' => function ($query) {
                    $query->select('product_id', \DB::raw('COUNT(*) as total_reviews'), \DB::raw('SUM(rating) as total_rating'))
                        ->groupBy('product_id');
                }
            ])
                ->has('valorations', '=', 0) // Obtén productos sin valoraciones
                ->limit(count($topProducts) - 3) // Limita a tres productos
                ->get();

            foreach ($topProducts2 as $value) {
                # code...
                $value->valorations = [
                    "product_id" => 3,
                    "total_reviews" => 1,
                    "total_rating" => "3"
                ];
            }

            $topProducts = $topProducts->concat($topProducts2);
        }
        return response()->json($topProducts);
    }

    public function getBySubCategorieId($sub_categorie_id)
    {
        $validator = Validator::make(['sub_categorie_id' => $sub_categorie_id], [
            'sub_categorie_id' => 'required|exists:subcategories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }


        $products = ProductsModel::where('sub_categorie_id', $sub_categorie_id)->get();

        return response()->json($products);
    }
}
