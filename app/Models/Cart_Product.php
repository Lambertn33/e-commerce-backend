<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart_Product extends Model
{
    use HasFactory;
    protected $fillable = [
        'id','cart_id','product_id','quantity','amount'
    ];
    protected $casts = [
        'id'=>'string',
        'cart_id'=>'string',
        'product_id'=>'string'
    ];
}
