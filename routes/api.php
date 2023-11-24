<?php

use App\Http\Controllers\EpaycoController;
use App\Http\Controllers\AuthController;
// use App\Http\Controllers\ProductsController;
use App\Http\Controllers\CategoriesController;
use App\Http\Controllers\PaymentLinksController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SubCategoriesController;
use App\Http\Controllers\ticketsController;
use App\Http\Controllers\ValorationsController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['auth:sanctum'])->group(function () {
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('generatePayMentLink', [EpaycoController::class, 'geneateLink']);
    Route::post('generatePayMentLink2', [EpaycoController::class, 'testApi']);
    Route::post('tickets', [ticketsController::class, 'store']);
    
Route::post('valorations/create', [ValorationsController::class, 'store']);
});

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::get('products', [ProductsController::class, 'index']);
Route::post('products/create', [ProductsController::class, 'store']);
Route::get('products/{id}', [ProductsController::class, 'show']);
Route::put('products/edit/{id}', [ProductsController::class, 'update']);
Route::delete('products/delete/{id}', [ProductsController::class, 'destroy']);
Route::get('products/find/top_rated', [ProductsController::class, 'getTopRatedProducts']);
Route::get('products/subcategorie/{sub_categorie_id}', [ProductsController::class, 'getBySubCategorieId']);

Route::get('valorations', [ValorationsController::class, 'index']);
Route::get('valorations/{id}', [ValorationsController::class, 'show']);
Route::put('valorations/edit/{id}', [ValorationsController::class, 'update']);
Route::delete('valorations/delete/{id}', [ValorationsController::class, 'destroy']);
Route::get('valorations/product/{productId}', [ValorationsController::class, 'getByProductId']);

Route::get('categories', [CategoriesController::class, 'index']);
Route::get('categories/{id}', [CategoriesController::class, 'show']);

Route::get('subcategories', [SubCategoriesController::class, 'index']);
Route::get('subcategories/{id}', [SubcategoriesController::class, 'show']);

Route::post('performsearch', [SearchController::class, 'search']);

Route::get('getLinkByReferenceCodeId/{id}', [PaymentLinksController::class, 'getByReferenceCodeId']);

