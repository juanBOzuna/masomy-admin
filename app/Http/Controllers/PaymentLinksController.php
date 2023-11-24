<?php

namespace App\Http\Controllers;

use App\Models\OrdenDetailModel;
use App\Models\OrdenModel;
use App\Models\PaymentLinksModel;
use App\Models\PreOrdenDetailModel;
use App\Models\PreOrdenModel;
use App\Models\ProductsModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentLinksController extends Controller
{
    //
    public function getByReferenceCodeId($reference)
    {
        // $reference = $request->input('code');
        $link = PaymentLinksModel::where('reference', $reference)->first();

        $order_type = '';

        $success = PaymentLinksModel::APROBADA;
        $success2 = PaymentLinksModel::PAGADO;
        $success3 = PaymentLinksModel::ACEPTADA;
        if ($link == null) {
            return response()->json(['success' => false, 'typeOrder' => 'ninguna']);
        }
        if ($link->status == $success || $link->status == $success2 || $link->status == $success3) {
            if ($link->have_order) {
                $order_type = 'Order';
            } else {
                $order_type = 'PreOrder';
            }
        }

        $orden_resp = null;
        $products = array();

        if ($order_type == 'Order') {
            $orden_resp = OrdenModel::where('payment_link_id', $link->id)->first();
            $products2 = OrdenDetailModel::where('orden_id', $orden_resp->id)->get();
            foreach ($products2 as $details) {
                # code...
                $producto = ProductsModel::where('id', $details->product_id)->first();

                $product = [
                    "detail" => $details,
                    "producto" => $producto
                ]
                ;
                array_push($products, $product);

            }
        } else {
            $orden_resp = PreOrdenModel::where('payment_link_id', $link->id)->first();
            if ($orden_resp != null) {
                $products2 = PreOrdenDetailModel::where('pre_orden_id', $orden_resp->id)->get();
                foreach ($products2 as $details) {
                    # code...
                    $producto = ProductsModel::where('id', $details->product_id)->first();

                    $product = [
                        "detail" => $details,
                        "producto" => $producto
                    ]
                    ;
                    array_push($products, $product);

                }
            } else {
            }

        }


        return response()->json(['success' => true, 'linkPay' => $link, 'typeOrder' => $order_type, 'orden' => $orden_resp, 'products' => $products]);
    }
}
