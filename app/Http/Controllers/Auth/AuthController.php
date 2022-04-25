<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Services\DataService;
use Illuminate\Http\Request;
use Validator;
use App\Models\User;
use App\Models\Account;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }
  
    public function login(Request $request){
    	$validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return Response::json([
                'status'=> 400,
                'message'=>'Please fill All Fields'
            ],400);
        }
        if (! $token = auth()->attempt($validator->validated())) {
            return Response::json([
                'status'=> 400,
                'message'=>'Invalid Email/Password'
            ],400);
        }
        return $this->createNewToken($token);
    }

    public function register(Request $request){
        $validator = Validator::make($request->all(), [
            'names' =>'required',
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);
        if ($validator->fails()) {
            return Response::json([
                'status'=> 400,
                'message'=>'Please fill All Fields'
            ],400);
        }
        DB::beginTransaction();
        $checkUserEmail = User::where('email',$request->email);
        if($checkUserEmail->exists()){
            return Response::json([
                'status'=> 400,
                'message'=>'Email has already been taken'
            ],400);
        }
        try {
            $newUser = [
                'id'=>Str::uuid()->toString(),
                'names'=>$request->names,
                'email'=>$request->email,
                'password'=>Hash::make($request->password),
                'created_at'=>now(),
                'updated_at'=>now(),
            ];
            //After creating the user , create his/her account also
            $newUserAccount = [
             'id'=>Str::uuid()->toString(),
             'user_id'=>$newUser['id'],
             'balance'=>0,
             'created_at'=>now(),
             'updated_at'=>now(),
            ];
            (new DataService)->createData(User::class,$newUser);
            (new DataService)->createData(Account::class,$newUserAccount);
            DB::commit();
            return Response::json([
                'status'=> 200,
                'message'=>'Registration done successfully',
                'user'=>$newUser
            ],200);
        } catch (\Throwable $th) {
            throw $th;
            DB::rollback();
            return Response::json([
                'status'=> 500,
                'message'=>'an error occured..please try again',
            ],500);
        }
    }

     /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout() {
        auth()->logout();
        return response()->json(['message' => 'User successfully signed out']);
    }
     /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function createNewToken($token){
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 216000,
            'user' => auth()->user()
        ]);
    }
}
