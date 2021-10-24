@extends('layouts.legacy')

@section('content')
    <div class="container-lg border-bottom border-lg-x rounded-bottom my-n4">
        <div class="row">
            @section('sidebar')
                <div class="d-none d-md-block col-md-4 col-lg-3 py-3 py-sm-4 px-sm-3 px-md-4 bg-light border-sm-right">
                    <h2 class="mb-5">Mon Compte</h2>

                    <nav class="nav nav-pills nav-fill flex-column">
                        <a class="nav-item nav-link text-left{{ Route::is('account.edit') ? ' active' : '' }}"
                           href="{{ route('account.edit') }}">
                            <svg class="bi bi-person-lines-fill mr-2" width="1em" height="1em" viewBox="0 0 16 16"
                                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 100-6 3 3 0 000 6zm7 1.5a.5.5 0 01.5-.5h2a.5.5 0 010 1h-2a.5.5 0 01-.5-.5zm-2-3a.5.5 0 01.5-.5h4a.5.5 0 010 1h-4a.5.5 0 01-.5-.5zm0-3a.5.5 0 01.5-.5h4a.5.5 0 010 1h-4a.5.5 0 01-.5-.5zm2 9a.5.5 0 01.5-.5h2a.5.5 0 010 1h-2a.5.5 0 01-.5-.5z"
                                      clip-rule="evenodd"/>
                            </svg>
                            Mes informations
                        </a>

                        <a class="nav-item nav-link text-left{{ Route::is('subscription.edit') ? ' active' : '' }}"
                           href="{{ route('subscription.edit') }}">
                            <svg class="bi bi-wallet2 mr-2" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M2.5 4l10-3A1.5 1.5 0 0 1 14 2.5v2h-1v-2a.5.5 0 0 0-.5-.5L5.833 4H2.5z"/>
                                <path fill-rule="evenodd" d="M1 5.5A1.5 1.5 0 0 1 2.5 4h11A1.5 1.5 0 0 1 15 5.5v8a1.5 1.5 0 0 1-1.5 1.5h-11A1.5 1.5 0 0 1 1 13.5v-8zM2.5 5a.5.5 0 0 0-.5.5v8a.5.5 0 0 0 .5.5h11a.5.5 0 0 0 .5-.5v-8a.5.5 0 0 0-.5-.5h-11z"/>
                            </svg>
                            Ma cotisation
                        </a>
                    </nav>
                </div>
            @show

            <div class="col py-4 px-sm-3 px-md-5 bg-white">
                <h3 class="font-weight-bold mt-4 mb-5">
                    @yield('section_title')
                </h3>

                @yield('section_content')
            </div>
        </div>
    </div>
@endsection
