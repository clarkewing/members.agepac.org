<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->query('name');

        return User::where(Builder::concat('`first_name`', '" "', '`last_name`'), 'LIKE', "%$search%")
            ->take(5)
            ->get();
    }
}
