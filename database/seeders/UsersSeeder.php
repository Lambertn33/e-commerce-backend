<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('users')->delete();

        \DB::table('users')->insert(array(
            0 => array(
                'id' =>Str::uuid()->toString(),
                'names'=>'System Administrator',
                'email'=>'admin@gmail.com',
                'password'=>Hash::make('admin12345'),
                'is_admin'=>true,
                'created_at'=>now(),
                'updated_at'=>now()
            )
        ));
    }
}
