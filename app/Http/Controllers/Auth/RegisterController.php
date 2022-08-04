<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Auth\AuthBaseController;
use App\Providers\RouteServiceProvider;
use Modules\Admin\Entities\Administrator;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class RegisterController extends AuthBaseController
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     *
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make(
            $data,
            [
                'login' => ['required', 'string', 'max:25'],
                'first_name' => ['required', 'string', 'max:50'],
                'middle_name' => ['string', 'max:50'],
                'last_name' => ['required', 'string', 'max:50'],
                'phone' => ['required', 'string', 'max:15'],
                'email' => [
                    'required',
                    'string',
                    'email',
                    // 'max:60', 'unique:Modules\Admin\Entities\Administrator,email',
                ],
                'password' => ['required', 'string', 'min:5', 'confirmed'],
            ]
        );
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     *
     * @return Administrator
     */
    protected function create(array $data)
    {
        return Administrator::create(
            [
                'login' => $data['login'],
                'first_name' => $data['first_name'],
                'middle_name' => $data['middle_name'],
                'last_name' => $data['last_name'],
                'phone' => $data['phone'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'created_at' => time(),
                'created_by' => 0,
            ]
        );
    }
}
