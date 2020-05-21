<nav class="navbar navbar-expand-md navbar-dark py-3 shadow-sm bg-primary">
    <div class="container">
        <a class="navbar-brand" href="{{ url('/') }}">
            <img src="{{ asset('logo_white.svg') }}" height="28" alt="AGEPAC">
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <!-- Left Side Of Navbar -->
            <ul class="navbar-nav mr-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-toggle="dropdown">
                        L'Association
                    </a>
                    <div class="dropdown-menu">
                        <a class="dropdown-item" href="#">À propos</a>
                        <a class="dropdown-item" href="#">Histoire</a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">Gouvernance</a>
                        <a class="dropdown-item" href="#">Status</a>
                    </div>
      </li>
                <li class="nav-item">
                    <a class="nav-link" href="#">Les EPL</a>
                </li>
                @auth
                    <li class="nav-item">
                        <a class="nav-link" href="#">Trajectoire EPL</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Réseau EPL</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Fly EPL</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('threads.index') }}">Forum</a>
                    </li>
                @endauth
            </ul>

            <!-- Right Side Of Navbar -->
            <ul class="navbar-nav ml-auto">
                <!-- Authentication Links -->
                @guest
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                    </li>
                    @if (Route::has('register'))
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                        </li>
                    @endif
                @else
                    <user-notifications></user-notifications>

                    <li class="nav-item dropdown">
                        <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                            {{ Auth::user()->name }} <span class="caret"></span>
                        </a>

                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="navbarDropdown">

                            <a class="dropdown-item" href="{{ route('profiles.show', Auth::user()) }}">
                                Mon Profil
                            </a>

                            <div class="dropdown-divider"></div>

                            <h6 class="dropdown-header">Mon Compte</h6>

                            <a class="dropdown-item" href="{{ route('account.edit') }}">
                                Mes informations
                            </a>

                            <div class="dropdown-divider"></div>

                            <logout-button class="dropdown-item" route="{{ route('logout') }}">
                                {{ __('Logout') }}
                            </logout-button>
                        </div>
                    </li>
                @endguest
            </ul>
        </div>
    </div>
</nav>
