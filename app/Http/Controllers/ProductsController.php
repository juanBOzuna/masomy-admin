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
        return response()->json(['succes'=> true]);
    }

    // Obtener un producto específico por ID
    public function show($id)
    {
        // Obtener el producto con sus valoraciones
        $product = ProductsModel::with('valorations')->find($id);

        if (!$product) {
            return response()->json(['message' => 'Producto no encontrado'], 404);
        }

        // Calcular la cantidad de valoraciones
        $totalReviews = $product->valorations->count();

        // Calcular el promedio de estrellas
        $averageRating = $totalReviews > 0 ? $product->valorations->avg('rating') : 0;

        // Agregar la información al arreglo del producto
        $product->total_reviews = $totalReviews;
        $product->average_rating = $averageRating;

        unset($product->valorations);


        return response()->json($product);
    }

    // Crear un nuevo producto
    public function store(Request $request)
    {
        $product = ProductsModel::create($request->all());
        return response()->json($product, 201);
    }

    // Actualizar un producto existente
    public function update(Request $request, $id)
    {
        $product = ProductsModel::find($id);
        $product->update($request->all());
        return response()->json($product, 200);
    }

    // Eliminar un producto
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

        // Ordenar los productos en memoria según la valoración promedio
        $topProducts = $topProducts->sortByDesc(function ($product) {
            $totalReviews = $product->valorations->first()->total_reviews ?? 0;
            $totalRating = $product->valorations->first()->total_rating ?? 0;

            return $totalReviews > 0 ? $totalRating / $totalReviews : 0;
        });

        // Tomar los primeros 3 productos
        $topProducts = $topProducts->take(3);

        // Obtener solo los valores y convertir a JSON
        return response()->json($topProducts->values());
    }

    public function getBySubCategorieId($sub_categorie_id)
    {
        $validator = Validator::make(['sub_categorie_id' => $sub_categorie_id], [
            'sub_categorie_id' => 'required|exists:subcategories,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        // $valorations = ValorationsModel::where('product_id', $sub_categorie_id)->get();
        $products = ProductsModel::where('sub_categorie_id', $sub_categorie_id)->get();

        return response()->json($products);
    }
}
