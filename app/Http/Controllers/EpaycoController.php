<?php

namespace App\Http\Controllers;

use App\Models\PaymentLinksModel;
use Illuminate\Support\Str;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
//use http\Client\Curl\User;
use App\Http\Repositories\Wallet\WalletController;
use App\Models\Briefcase;
use App\Models\PaymentAttempts;
use App\Models\ProductsModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\User;

class EpaycoController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function geneateLink(Request $request)
    {

        try {


            $productIds = collect($request->products)->pluck('id')->toArray();
            // }
            $existingProducts = ProductsModel::whereIn('id', $productIds)
                ->where('is_active', true)
                ->get();

            // Obtener IDs de productos que no existen o no están activos
            $nonExistingProductIds = array_diff($productIds, $existingProducts->pluck('id')->toArray());
            // dd($nonExistingProductIds);

            // Verificar si hay productos que no existen o no están activos
            if (!empty($nonExistingProductIds)) {
                return response()->json([
                    'success' => false,
                    'payLink' => null,
                    'message' => 'Los productos con los IDs ' . implode(', ', $nonExistingProductIds) . ' no existen o no están activos.',
                    'noExistingProducts' => $nonExistingProductIds
                ], 422);

                // throw new \Exception('Los productos con los IDs ' . implode(', ', $nonExistingProductIds) . ' no existen o no están activos.');
            }

            $grand_total = 0;
            $text_description = implode(', ', array_map(function ($product) use ($request) {
                $quantity = collect($request->products)->firstWhere('id', $product['id'])['quantity'];
                return $quantity . ' ' . $product['name'];
            }, $existingProducts->toArray()));


            $grand_total = $existingProducts->sum('price');
            $llaveDePago = $this->generarLlaveDePago();
            // dd($text_description);

            $token = self::login()->token;
            // return response()->json(['success' => true, 'url' => $token], 200);
            $user = [
                'id' => auth()->user()->id,
                'email' => auth()->user()->email
            ];
            $curl = curl_init();
            curl_setopt_array(
                $curl,
                array(
                    CURLOPT_URL => 'https://apify.epayco.co/collection/link/create',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'POST',
                    CURLOPT_POSTFIELDS => '{
              "quantity": 1,
              "onePayment":true,
              "amount": "' . $grand_total . '",
              "currency": "COP",
              "id": "0",
              "reference": "' . $llaveDePago . '",
              "base": "0",
              "description": "' . $text_description . '",
              "title": "COBRO TIENDA MASOMY",
              "typeSell": "1",
              "tax": "0",
              "email": "' . $user['email'] . '",
              "urlResponse": "http://localhost/masomy_ecommerce/"
            }',
                    CURLOPT_HTTPHEADER => array(
                        'Accept: application/json',
                        'Content-Type: application/json',
                        'Authorization: Bearer ' . $token
                    )
                )
            );

            $response = json_decode(curl_exec($curl));
            // return response()->json(['success' => true, 'url' => $response], 200);
            curl_close($curl);
            if ($response->success) {
                PaymentLinksModel::create([
                    'reference' => $llaveDePago,
                    'link' => $response->data->routeLink,
                    'user_id' => $user['id']
                ]);
            }
            $url = $response->data->routeLink;
            // dd();
            return response()->json(['success' => true, 'url' => $url, 'other' => 'other'], 200);


        } catch (\Exception $e) {

            return response()->json(['success' => false, 'payLink' => null, 'message' => $e->getMessage()], 400);

        }

    }

    public function testApi()
    {
        return response()->json(['success' => false]);
    }

    private function generarLlaveDePago()
    {
        $fechaActual = now();
        $fechaFormateada = $fechaActual->format('YmdHis');
        $llaveDePago = $fechaFormateada . Str::random(5);
        return $llaveDePago;
    }

    //     static function checkEpaycoStatus($id = null,$write = null){

    //         $payments = PaymentAttempts::where('userID',$id)
//             ->where('status',"!=",PaymentAttempts::ACEPTADA)
//             ->where('status',"!=",PaymentAttempts::CANCELADA)
//             ->where('status',"!=",PaymentAttempts::ABANDONADA)
//             ->where('status',"!=",PaymentAttempts::FALLIDA)
//             ->where('status',"!=",PaymentAttempts::NO_PAGADO)
//             ->where('status',"!=",PaymentAttempts::RECHAZADA)
//             ->where('status',"!=",PaymentAttempts::PAGADO)
//             ->where('status',"!=",PaymentAttempts::APROBADA)
//             ->whereBetween('created_at', [
//                 now()->subWeeks(3),
//                 now()
//             ])->get();


    //         if($write){
//             $write->info(json_encode($payments));
//         }


    //         if(count($payments) > 0){
//             $token = self::login()->token;
//         }
//         foreach ($payments as $pay){


    //             $response = Http::withHeaders([
//                 'Accept' => 'application/json',
//                 'Content-Type' => 'application/json',
//                 'Authorization' => 'Bearer '.$token,
//             ])->post('https://apify.epayco.co/transaction',['filter' => ['referenceClient' => $pay->payRef]]);

    //             $referenses = json_decode($response)->data->data;

    //             if (count($referenses) >= 1) {

    //                 if( $referenses[0]->status == PaymentAttempts::PAGADO || $referenses[0]->status == PaymentAttempts::APROBADA || $referenses[0]->status == PaymentAttempts::ACEPTADA  ){

    //                   if($write){
//                     $write->info(json_encode($pay));
//                   }

    //                   WalletController::make($id,$pay->amount,WalletController::IN,"Recarga desde la App.");

    //                 }

    //                 $PaymentAttempts = PaymentAttempts::where('_id',$pay->id)->first();
//                 $PaymentAttempts->status = $referenses[0]->status;
//                 $PaymentAttempts->update();

    //             }

    //         }

    //         $wallet =  WalletController::getWallet($id);

    //         if( $wallet->technical_id == null  ){

    //             $wallet = WalletController::createWallet($id);

    //         }

    //         return $wallet;

    //     }

    private static function login()
    {

        $curl = curl_init();

        curl_setopt_array(
            $curl,
            array(
                CURLOPT_URL => 'https://apify.epayco.co/login',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_HTTPHEADER => array(
                    'Authorization: Basic ' . env('AUTHORIZATION_EPAYCO')
                ),
            )
        );

        $response = curl_exec($curl);

        curl_close($curl);
        return json_decode($response);

    }

    //     private function generateLinkEpayco($token, $gandTotal, $data)
//     {

    // //        dd($data['description']);
//         $curl = curl_init();
//         curl_setopt_array($curl, array(
//             CURLOPT_URL => 'https://apify.epayco.co/collection/link/create',
//             CURLOPT_RETURNTRANSFER => true,
//             CURLOPT_ENCODING => '',
//             CURLOPT_MAXREDIRS => 10,
//             CURLOPT_TIMEOUT => 0,
//             CURLOPT_FOLLOWLOCATION => true,
//             CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
//             CURLOPT_CUSTOMREQUEST => 'POST',
//             CURLOPT_POSTFIELDS => '{
//               "quantity": 1,
//               "onePayment":true,
//               "amount": "'. $gandTotal .'",
//               "currency": "COP",
//               "id": "0",
//               "reference": "'. $data['payRef'] .'",
//               "base": "0",
//               "description": "'. $data['description'] .'",
//               "title": "'. $data['title'] .'",
//               "typeSell": "1",
//               "tax": "0",
//               "email": "'. $data['email'] .'",
//               "urlResponse": "'. $data['urlResponse'] .'"
//             }',
//             CURLOPT_HTTPHEADER => array(
//                 'Accept: application/json',
//                 'Content-Type: application/json',
//                 'Authorization: Bearer ' . $token
//             )
//         ));

    //         $response = curl_exec($curl);

    // //        dd($response);
//         curl_close($curl);
//         return json_decode($response);
//     }


    //     private function minutesDifference($date) {
//         $now = Carbon::now();
//         $givenDate = Carbon::parse($date);
//         return $now->diffInMinutes($givenDate);
//     }

}
