<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Services\DataService;
use Illuminate\Support\Str;
use App\Models\Product;
use App\Models\Cart;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Validator;

class PurchaseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function purchaseProduct(Request $request ,$id)
    {
        $user = Auth::user();
        if($user->is_admin){
            return Response::json([
                'status'=> 401,
                'message'=>'unauthorized'
            ],401);
       }
        $productToPurchase = Product::find($id);
        if(!$productToPurchase){
            return Response::json([
                'status'=> 404,
                'message'=>'product not found'
            ],404);
        }
        $validator = Validator::make($request->all(), [
            'quantity' =>'required',
        ]);
        if ($validator->fails()) {
            return response()->json($validator->errors(), 422);
        }
        try {
            DB::beginTransaction();
            $checkCartExistence = Cart::where('user_id',$user->id);
            //validate user amount to check if the product can be affordable
            $userBalance = Auth::user()->account->balance;
            $totalAmountToPay = $productToPurchase->price * $request->quantity;
            if($userBalance < $totalAmountToPay){
                return Response::json([
                    'status'=> 400,
                    'message'=>'Dear '. Auth::user()->names .' you do not have sufficient balance'
                ],400); 
            }
            //discounts based on total amount to pay
            if($totalAmountToPay > 112 && $totalAmountToPay < 115){
                $totalAmountToPay = $totalAmountToPay - (($totalAmountToPay * 0.25)/100);
            }elseif($totalAmountToPay > 120){
                $totalAmountToPay = $totalAmountToPay - (($totalAmountToPay * 0.5)/100);
            }           
            //create the cart for the user if not exists
            if(!$checkCartExistence->exists()){
                $newCart = [
                    'id'=>Str::uuid()->toString(),
                    'user_id'=>$user->id,
                    'created_at'=>now(),
                    'updated_at'=>now()
                ];
                (new DataService)->createData(Cart::class,$newCart);
                DB::commit();
                }
                $Cart = Auth::user()->cart()->first();
                $Cart->products()->attach($productToPurchase,array(
                    'id'=>Str::uuid()->toString(),
                    'quantity'=>$request->quantity,
                    'amount'=>$totalAmountToPay,
                    'created_at'=>now(),
                    'updated_at'=>now()
                ));
                //update User Balance 
                $newBalance = [
                    'balance'=>$userBalance - $totalAmountToPay
                ];
                (new DataService)->updateData(Account::class,$newBalance,$user->id); 

                //save this Transaction
                $message = 'bought '. $request->quantity .' pieces of '. $productToPurchase->name .' at '. $totalAmountToPay . ' ';
                //Transaction Done
                $newTransaction = [
                    'id'=>Str::uuid()->toString(),
                    'user_id'=>$user->id,
                    'description' =>$message,
                    'amount' => $totalAmountToPay,
                    'created_at'=>now(),
                    'updated_at'=>now(),
                ];
                //Save this transaction of topping up balance
             (new DataService)->createData(Transaction::class , $newTransaction);
              DB::commit();
              return Response::json([
                'status'=> 200,
                'message'=>'Purchase done successfully'
            ],200);

        } catch (\Throwable $th) {
            DB::rollback();
            return Response::json([
                'status'=> 500,
                'message'=>'an error occured...please try again'
            ],500); 
        }
    }

    public function viewMyPurchases()
    {
        if(Auth::user()->is_admin){
            return Response::json([
                'status'=> 401,
                'message'=>'unauthorized'
            ],401);
       }
        $model = Cart::with('products')->where('user_id',Auth::user()->id);
        return (new DataService)->getData($model);
    }
    

}
