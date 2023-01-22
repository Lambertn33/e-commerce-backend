<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\ProductsController;
use App\Http\Controllers\Customer\CustomerProductsController;
use App\Http\Controllers\Customer\CustomerAccountController;
use App\Http\Controllers\Customer\PurchaseController;
use App\Http\Controllers\Auth\AuthController;

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
Route::controller(AuthController::class)->group(function(){
    Route::group(['middleware'=>'api','prefix'=>'auth'],function($router){
        Route::post('/login',  'login');
        Route::post('/register', 'register');
        Route::post('/logout','logout');        
    });
});

Route::controller(ProductsController::class)->group(function(){
    Route::prefix('admin/products')->group(function(){
        Route::get('/','getAllProducts');
        Route::post('/','createNewProduct');
    });
});
Route::controller(CustomerProductsController::class)->group(function(){
    Route::prefix('/products')->group(function(){
        Route::get('/','getAllProducts');
        Route::get('/{id}','getSingleProduct');
        
    });
});
Route::controller(PurchaseController::class)->group(function(){
    Route::prefix('/products')->group(function(){
        Route::post('/{id}/purchase','purchaseProduct');  
        Route::get('/myPurchases','viewMyPurchases');      
    });
});
Route::controller(CustomerAccountController::class)->group(function(){
    Route::prefix('/my-account')->group(function(){
        Route::get('/','getMyAccount');
        Route::get('/transactions','getMyTransactions');
        Route::put('/topUp','topUpMyAccount');
        Route::post('/emailTest','sendEmailTest');
        
    });
});
