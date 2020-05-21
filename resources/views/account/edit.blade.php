@extends('layouts.app')

@section('content')
    <div class="container-lg border-bottom border-lg-x rounded-bottom my-n4">
        <div class="row">
            @section('sidebar')
                <div class="col-md-4 col-lg-3 py-3 py-sm-4 px-sm-3 px-md-4 bg-light border-sm-right">
                    <h2 class="mb-5">Mon Compte</h2>

                    <nav class="nav nav-pills nav-fill flex-column">
                        <a class="nav-item nav-link text-left{{ Route::is('account.edit') ? ' active' : '' }}"
                           href="{{ route('account.edit') }}">
                            <svg class="bi bi-person-lines-fill mr-2" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd" d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1H1zm5-6a3 3 0 100-6 3 3 0 000 6zm7 1.5a.5.5 0 01.5-.5h2a.5.5 0 010 1h-2a.5.5 0 01-.5-.5zm-2-3a.5.5 0 01.5-.5h4a.5.5 0 010 1h-4a.5.5 0 01-.5-.5zm0-3a.5.5 0 01.5-.5h4a.5.5 0 010 1h-4a.5.5 0 01-.5-.5zm2 9a.5.5 0 01.5-.5h2a.5.5 0 010 1h-2a.5.5 0 01-.5-.5z" clip-rule="evenodd"/>
                            </svg>
                            Mes informations
                        </a>
                    </nav>
                </div>
            @show

            <div class="col py-4 px-sm-3 px-md-5 bg-white">
                <h3 class="font-weight-bold mt-4 mb-5">Mes informations</h3>

                <h5 class="font-weight-bold pb-2 border-bottom mb-4">Informations personnelles</h5>

                <div class="row mb-5">
                    <div class="col-md-4">
                        <p>
                            On essaiera de te souhaiter un joyeux anniversaire tous les ans.
                            Ne nous tape pas dessus si on oublie, c'est que
                            <a href="https://www.linkedin.com/in/mrhugo/" target="_blank" title="Mec mega trop chouette">Hugo</a>
                            a oublié de coder la fonctionalité !
                        </p>
                    </div>

                    <div class="col-md-8">
                        <form action="{{ route('account.update') }}" method="post">
                            @csrf
                            @method('patch')

                            <div class="form-row">
                                <div class="col-6 form-group mb-1">
                                    <label for="first_name" class="small mb-0">Prénom</label>
                                    <input type="text" class="form-control" id="first_name"
                                           value="{{ $user->first_name }}"
                                           aria-describedby="nameHelpBlock" disabled>
                                </div>

                                <div class="col-6 form-group mb-1">
                                    <label for="first_name" class="small mb-0">Nom de famille</label>
                                    <input type="text" class="form-control" id="last_name"
                                           value="{{ $user->last_name }}"
                                           aria-describedby="nameHelpBlock" disabled>
                                </div>
                            </div>
                            <small id="nameHelpBlock" class="form-text text-muted mb-3">
                                <a href="mailto:webmaster@agepac.org" title="Envoyer un email au Webmaster">
                                    Écris-nous
                                </a>
                                pour modifier ces données.
                            </small>

                            <div class="form-row">
                                <div class="col-6 form-group">
                                    <label for="birthdate" class="small mb-1">Date de naissance</label>
                                    <input type="date" class="form-control @error('birthdate') is-invalid @enderror"
                                           id="birthdate" name="birthdate"
                                           value="{{ old('birthdate', $user->birthdate) }}">
                                </div>
                            </div>
                            @error('birthdate')
                                <div class="d-block mt-n2 mb-3 invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-row">
                                <div class="col-6 form-group">
                                    <label for="phone" class="small mb-1">Numéro de téléphone</label>
                                    <input type="tel" class="form-control @error('phone') is-invalid @enderror"
                                           id="phone" name="phone"
                                           value="{{ old('phone', $user->phone->formatInternational()) }}">
                                </div>
                            </div>
                            @error('phone')
                                <div class="d-block mt-n2 mb-3 invalid-feedback">{{ $message }}</div>
                            @enderror

                            <button type="submit" class="btn btn-success">Sauvegarder</button>
                        </form>
                    </div>
                </div>

                <h5 class="font-weight-bold pb-2 border-bottom mb-4">Identifiants</h5>

                <div class="row">
                    <div class="col-md-4">
                        <p class="mb-2">
                            On te conseille d'utiliser ton adresse email personelle.
                        </p>
                        <p>
                            En ce qui concerne le mot de passe, sois {{ $user->gender === 'F' ? 'futée' : 'futé' }},
                            choisis quelque chose de super mega secret et sûr.<br>
                            Voici
                            <a href="https://xkcd.com/936/" target="_blank" title="XKCD Password Strength">une petite BD</a>
                            trop chouette pour t'aider à choisir.
                        </p>
                    </div>

                    <div class="col-md-8">
                        <form action="{{ route('account.update') }}" method="post">
                            @csrf
                            @method('patch')

                            <div class="form-row">
                                <div class="col-6 form-group mb-1">
                                    <label for="first_name" class="small mb-0">Adresse email</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror"
                                           id="email" name="email"
                                           value="{{ old('email', $user->email) }}"
                                           aria-describedby="emailHelpBlock">
                                </div>
                            </div>
                            @error('email')
                                <div class="d-block mb-3 invalid-feedback">{{ $message }}</div>
                            @else
                                <small id="emailHelpBlock" class="form-text text-muted mb-3">
                                    Tu devras revérifier ton adresse email si tu la modifies.
                                </small>
                            @enderror

                            <div class="form-row">
                                <div class="col-6 form-group">
                                    <label for="current_password" class="small mb-1">Mot de passe actuel</label>
                                    <input type="password"
                                           class="form-control @error('current_password') is-invalid @enderror"
                                           id="current_password" name="current_password"
                                           required autocomplete="current-password">
                                </div>
                            </div>
                            @error('current_password')
                                <div class="d-block mt-n3 mb-3 invalid-feedback">{{ $message }}</div>
                            @enderror

                            <div class="form-row">
                                <div class="col-6 form-group mb-1">
                                    <label for="new_password" class="small mb-1">Nouveau mot de passe</label>
                                    <input type="password"
                                           class="form-control @error('new_password') is-invalid @enderror"
                                           id="new_password" name="new_password"
                                           autocomplete="new-password">
                                </div>

                                <div class="col-6 form-group mb-1">
                                    <label for="new_password_confirmation" class="small mb-1">Confirmation</label>
                                    <input type="password" class="form-control"
                                           id="new_password_confirmation" name="new_password_confirmation"
                                           autocomplete="new-password">
                                </div>
                            </div>
                            @error('new_password')
                                <div class="d-block mt-n1 mb-3 invalid-feedback">{{ $message }}</div>
                            @else
                                <small id="emailHelpBlock" class="form-text text-muted mb-3">
                                    Laisse ces champs vides si tu ne souhaites pas modifier ton mot de passe.<br>
                                    Choisis quelque chose de sûr et d'au moins 8 caractères sinon.
                                </small>
                            @enderror

                            <button type="submit" class="btn btn-success">Sauvegarder</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
