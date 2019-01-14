<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Auth;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */

    // redirect to after login
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('guest')->except('logout');
        $this->middleware('guest',['except'=>['logout','userLogout']]);
    }


    //override start
    public function showLoginForm()
    {
        session(['link' => url()->previous()]);
        return view('auth.login');
    }
    protected function authenticated()
    {
        return redirect(session('link'));
    }
    //override end




    public function userLogout()
    {

        Auth::guard('web')->logout();

        return redirect('/');
        
    }
}
