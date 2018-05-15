<?php

namespace Illuminate\Foundation\Auth;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Auth\Events\Registered;
use App\Kart;
use App\sessionCart;
use Session;

trait RegistersUsers
{
    use RedirectsUsers;

    /**
     * Show the application registration form.
     *
     * @return \Illuminate\Http\Response
     */
    public function showRegistrationForm()
    {
        return view('auth.login');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        // 如果註冊失敗
        Session::flash('regFail','註冊失敗');
        $this->validator($request->all())->validate();

        

        event(new Registered($user = $this->create($request->all())));

        $this->guard()->login($user);

        if ($request->reg_buy == 0) {
            return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());    
        }else{

            $ip_address = request()->ip();
            $sessionCart=sessionCart::where('ip_address',$ip_address)->first();
            if ($sessionCart) {
                $items=json_decode($sessionCart->item);

                foreach ($items as $item) {
                    $kart = new Kart;
                    $kart->user_id = $user->id;
                    $kart->product_id = $item;
                    $kart->save();
                }
            sessionCart::where('ip_address',$ip_address)->delete();

            }else{
                return $this->registered($request, $user)
                        ?: redirect($this->redirectPath());
            }

            return redirect()->route('kart.index');

        }
        

        
    }

    /**
     * Get the guard to be used during registration.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }

    /**
     * The user has been registered.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
    protected function registered(Request $request, $user)
    {
        //
    }
}
