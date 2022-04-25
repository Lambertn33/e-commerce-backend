<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Services\DataService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class ProductsController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth:api');
    }
    protected $model = Product::class;

    public function getAllProducts()
    {
        if(!Auth::user()->is_admin){
            return Response::json([
                'status'=> 401,
                'message'=>'unauthorized'
            ],401);
       }
       return (new DataService)->getData($this->model);
    }

    public function createNewProduct(Request $request)
    {
       if(!Auth::user()->is_admin){
            return Response::json([
                'status'=> 401,
                'message'=>'unauthorized'
            ],401);
       }
       DB::beginTransaction();
       try {
        $data = $request->all();
        //manual Validation
 
        if(!$data['name'] || !$data['price']){
            return Response::json([
                'status'=> 400,
                'message'=>'please provide the name and price'
            ],400);
        }
        $checkProductName = Product::where('name',$request->name);
        if($checkProductName->exists()){
            return Response::json([
                'status'=> 400,
                'message'=>'Product Name has already been taken'
            ],400);
        } 
         $newProduct = [
            'id' =>Str::uuid()->toString(),
            'name'=> $data['name'],
            'price'=>$data['price'],
            'created_at'=>now(),
            'updated_at'=>now()
        ];
        (new DataService)->createData($this->model,$newProduct);
        DB::commit();
        return Response::json([
             'status'=> 200,
             'message'=>'new product created successfully',
             'product'=>$newProduct
         ],200);
       } catch (\Throwable $th) {
           DB::rollback();
           return Response::json([
            'status'=> 500,
            'message'=>'an error occured..please try again'
        ],500);
       }
    }
}
