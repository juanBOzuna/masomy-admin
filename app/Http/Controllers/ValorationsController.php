<?php

namespace App\Http\Controllers;

use App\Models\ValorationsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ValorationsController extends Controller
{
    /**
     * Muestra todas las valoraciones.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $valorations = ValorationsModel::all();
        return response()->json($valorations);
    }

    /**
     * Muestra la valoración específica por ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $valuation = ValorationsModel::find($id);

        if (!$valuation) {
            return response()->json(['error' => 'Valoración no encontrada'], 404);
        }

        return response()->json($valuation);
    }

    /**
     * Almacena una nueva valoración.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer',
            'comment' => 'nullable|string',
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $user = Auth::user(); // asumiendo que tienes autenticación configurada
        $valoration_old = ValorationsModel::where('user_id', $user->id)->where('product_id', $request->input('product_id'))->first();
        if ($valoration_old == null) {
            $valuation = new ValorationsModel([
                'rating' => $request->input('rating'),
                'comment' => $request->input('comment'),
                'user_id' => $user->id,
                'product_id' => $request->input('product_id'),
            ]);

            $valuation->save();
        } else {
            return response()->json(['success' => false, 'message' => 'Ya ha valorado este producto.', 'user' => $user, 'old' => $valoration_old,], 200);
        }

        return response()->json(['success' => true, 'message' => 'Valoracion Exitosa'], 200);
    }

    /**
     * Actualiza la valoración específica por ID.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer',
            'comment' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $valuation = ValorationsModel::find($id);

        if (!$valuation) {
            return response()->json(['error' => 'Valoración no encontrada'], 404);
        }

        // Verifica que el usuario autenticado sea el propietario de la valoración
        if (Auth::id() !== $valuation->user_id) {
            return response()->json(['error' => 'No tienes permisos para editar esta valoración'], 403);
        }

        $valuation->update([
            'rating' => $request->input('rating'),
            'comment' => $request->input('comment'),
        ]);

        return response()->json($valuation, 200);
    }

    /**
     * Elimina la valoración específica por ID.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $valuation = ValorationsModel::find($id);

        if (!$valuation) {
            return response()->json(['error' => 'Valoración no encontrada'], 404);
        }

        // Verifica que el usuario autenticado sea el propietario de la valoración
        if (Auth::id() !== $valuation->user_id) {
            return response()->json(['error' => 'No tienes permisos para eliminar esta valoración'], 403);
        }

        $valuation->delete();

        return response()->json(null, 204);
    }


    /**
     * Obtiene todas las valoraciones de un producto por su ID.
     *
     * @param  int  $productId
     * @return \Illuminate\Http\Response
     */
    public function getByProductId($productId)
    {
        $validator = Validator::make(['product_id' => $productId], [
            'product_id' => 'required|exists:products,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 400);
        }

        $valorations = ValorationsModel::where('product_id', $productId)->get();

        return response()->json($valorations);
    }
}
