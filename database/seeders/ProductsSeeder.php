<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('products')->delete();

        \DB::table('products')->insert(array(
            0 => array(
                'id' =>Str::uuid()->toString(),
                'name'=>'Product 1',
                'price'=> 1000,
                'created_at'=>now(),
                'updated_at'=>now()
            ),
            1 => array(
                'id' =>Str::uuid()->toString(),
                'name'=>'Product 2',
                'price'=> 114,
                'created_at'=>now(),
                'updated_at'=>now()
            ),
            2 => array(
                'id' =>Str::uuid()->toString(),
                'name'=>'Product 3',
                'price'=> 70,
                'created_at'=>now(),
                'updated_at'=>now()
            ),
        ));
    }
}
