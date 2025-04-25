<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
// Rimuoviamo il riferimento al RouteServiceProvider
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

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
    // Modifichiamo il redirectTo per usare direttamente il percorso
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        // In Laravel 12, i middleware vengono applicati nelle route
        // Non utilizzare più $this->middleware() qui
    }
}
