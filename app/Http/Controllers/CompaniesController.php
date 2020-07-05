<?php

namespace App\Http\Controllers;

use App\Company;
use App\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Illuminate\Validation\Rule;

class CompaniesController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display a listing of the resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\View\View
     */
    public function index(Request $request)
    {
            if ($request->has('query')) {
                return Company::search($request->query('query'))
                    ->paginate(25);
            }

            return Company::paginate(25);
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $company
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\Response
     */
    public function show(Request $request, Company $company)
    {
        if ($request->wantsJson()) {
            return Response::json($company);
        }

        return Response::view('companies.show', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Company  $company
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, Company $company)
    {
        $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type_code' => [
                'sometimes',
                'required',
                Rule::in(array_keys(Company::typeStrings())),
            ],
            'website' => 'sometimes|nullable|url|max:255',
            'description' => 'sometimes|required|string|max:65535',
            'operations' => 'sometimes|nullable|string|max:65535',
            'conditions' => 'sometimes|nullable|string|max:65535',
            'remarks' => 'sometimes|nullable|string|max:65535',
        ]);

        $company->update($request->all());

        return Response::json($company);
    }
}
