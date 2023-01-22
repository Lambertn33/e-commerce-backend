<?php

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Services\DataService;
use Illuminate\Support\Str;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;

class CustomerProductsController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }
    protected $model = Product::class;

    public function getAllProducts()
    {
       return (new DataService)->getData($this->model);
    }
    public function getSingleProduct($id)
    {
        return (new DataService)->viewData($this->model,$id);
    }
}
