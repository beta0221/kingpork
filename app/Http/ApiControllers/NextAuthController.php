<?php

namespace App\Http\ApiControllers;

namespace App\Http\ApiControllers;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NextAuthController extends Controller {


    public function login(Request $request)
    {
    	//validate the form data
    	$this->validate($request,[
    		'email' => 'required|email',
    		'password' => 'required|min:4'
    	]);
    

        $credentials = request(['email', 'password']);

        if(!Auth::attempt($credentials)) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }
            
        $user = $request->user();
        $tokenResult = $user->createToken('Personal Access Token');
        $token = $tokenResult->token;
        $token->expires_at = Carbon::now()->addWeeks(1);
        $token->save();

        return response()->json([
            'access_token' => $tokenResult->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse(
                $tokenResult->token->expires_at
            )->toDateTimeString()
        ]);
    		
    }

    public function user(Request $request)
    {
        $user = $request->user();

        return response($user);
    }


}