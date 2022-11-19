<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AccountSecurityController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware(['auth']);
    }

    public function __invoke(Request $request)
    {
        return view('account.security', [
            'request' => $request,
            'user' => $request->user(),
        ]);
    }
}
