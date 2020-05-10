<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\UserInvitation;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class UserInvitationsController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('throttle:5,1');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        // Allow search by full name or all params.
        $request->validate([
            'name' => 'required_without_all:first_name,last_name,class_course,class_year',

            'first_name' => 'required_without:name',
            'last_name' => 'required_without:name',
            'class_course' => ['required_without:name', Rule::in(config('council.courses'))],
            'class_year' => ['required_without:name', 'digits:4'],
        ]);

        if (! $request->has('name')) {
            $result = UserInvitation::where([
                ['first_name', 'LIKE', $request->query('first_name')],
                ['last_name', 'LIKE', $request->query('last_name')],
                ['class_course', '=', $request->query('class_course')],
                ['class_year', '=', $request->query('class_year')],
            ])->first();
        } else {
            $result = UserInvitation::where(
                Builder::concat('`first_name`', '" "', '`last_name`'), 'LIKE', $request->query('name')
            )->first();
        }

        return Response::json($result);
    }
}
