<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LoginController extends Controller
{
   public function login()
    {
        return view('login');
    }
   public function logout()
    {
        return redirect('/login')->with('message', 'You have been logged out successfully.');
    }

    public function authenticate(Request $request)
    {
       
        // Authentication failed
        return redirect()->back()->withErrors(['email' => 'Invalid credentials'])->withInput();
    }
}
