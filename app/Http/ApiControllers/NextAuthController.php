<?php

namespace App\Http\ApiControllers;

namespace App\Http\ApiControllers;
use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NextAuthController extends Controller {

    /**
     * 註冊
     */
    public function signup(Request $request) {

        //validate the form data
    	$this->validate($request,[
    		'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'phone' => 'required|max:255|string',
    	]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'phone' => $request->phone,
        ]);

        $user->save();

        return response([
            'message' => 'Successfully created user!'
        ], 201);
    }

    /**
     * 登入 
     */
    public function login(Request $request) {
    	
        //validate the form data
    	$this->validate($request,[
    		'email' => 'required|email',
    		'password' => 'required|min:4'
    	]);
    

        $credentials = request(['email', 'password']);

        if(!Auth::attempt($credentials)) {
            return response([
                'message' => 'Unauthorized'
            ], 401);
        }
            
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();

        return response([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    		
    }

    /**
     * 登出
     */
    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response([
            'message' => 'Successfully logged out'
        ]);
    }


    public function user(Request $request)
    {
        $user = $request->user();

        return response($user);
    }


}