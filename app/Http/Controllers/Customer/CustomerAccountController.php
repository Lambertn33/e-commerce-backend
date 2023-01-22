<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Services\DataService;
use Illuminate\Support\Str;
use App\Models\Account;
use App\Models\Transaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Validator;

class CustomerAccountController extends Controller
{
    protected $model = Account::class;

    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function getMyAccount()
    {
        $user = Auth::user();
        if($user->is_admin){
            return Response::json([
                'status'=> 401,
                'message'=>'unauthorized'
            ],401);
       }
        return $user->account()->first();
    }
    public function topUpMyAccount(Request $request)
    {
        $user = Auth::user();
        if($user->is_admin){
            return Response::json([
                'status'=> 401,
                'message'=>'unauthorized'
            ],401);
       }
        $validator = Validator::make($request->all(), [
            'balance' =>'required',
        ]);
        if ($validator->fails()) {
            return Response::json([
                'status'=> 400,
                'message'=>'Please provide the balance',
            ],400);
        }
        try {
            DB::beginTransaction();
            $myExistingBalance = $this->model::where('user_id',$user->id)->value('balance');
            $data = [
                'balance' =>$request->balance + $myExistingBalance
            ];
            $message = 'top up amount of '. $request->balance .' ';
            //Transaction Done
            $newTransaction = [
                'id'=>Str::uuid()->toString(),
                'user_id'=>$user->id,
                'description' =>$message,
                'amount' => $request->balance,
                'created_at'=>now(),
                'updated_at'=>now(),
            ];
            // Top Up Balance
            (new DataService)->updateData($this->model , $data , $user->id);
            //Save this transaction of topping up balance
            (new DataService)->createData(Transaction::class , $newTransaction);

            DB::commit();
            $myAccount = $this->model::where('user_id',$user->id)->first();
            return Response::json([
                'status'=> 200,
                'message'=>'Balance topped up successfully',
                'account'=>$myAccount
            ],200);
        } catch (\Throwable $th) {
            DB::rollback();
            return Response::json([
                'status'=> 500,
                'message'=>'an error occured..please try again',
            ],500);
        }
    }

    public function getMyTransactions()
    {
        $user = Auth::user();
        if($user->is_admin){
            return Response::json([
                'status'=> 401,
                'message'=>'unauthorized'
            ],401);
       }
        $this->model = Transaction::class;
        return (new DataService)->getData($this->model::where('user_id',$user->id));
        
    }
    public function sendEmailTest()
    {
        
    }
}
