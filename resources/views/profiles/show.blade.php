@extends('layouts.app')

@section('content')
    <div class="container text-primary bg-white mt-n4 py-4 border border-top-0 rounded-bottom">
        <div class="row">
            <div class="col-md-4 col-lg-3 offset-md-0 offset-lg-1">
                <avatar-form classes="rounded-circle w-50 mx-auto mb-3" :user="{{ $profileUser }}"></avatar-form>

                <h1 class="h2 font-weight-bold text-center">
                    {{ $profileUser->name }}
                </h1>

                <h5 class="text-center">
                    {{ $profileUser->class }}
                </h5>

                <h6 class="text-center mb-4">
                    Réputation : {{ $profileUser->reputation }} XP
                </h6>

                <ul class="list-unstyled small font-weight-bold mx-4 mx-md-0 mb-5">
                    @if($profileUser->hasOccupation())
                        <li class="d-flex align-items-center p-3 border-bottom">
                            <svg class="bi bi-briefcase flex-shrink-0 mr-3" width="1em" height="1em" viewBox="0 0 16 16"
                                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M0 12.5A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-6h-1v6a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-6H0v6z"/>
                                <path fill-rule="evenodd"
                                      d="M0 4.5A1.5 1.5 0 0 1 1.5 3h13A1.5 1.5 0 0 1 16 4.5v2.384l-7.614 2.03a1.5 1.5 0 0 1-.772 0L0 6.884V4.5zM1.5 4a.5.5 0 0 0-.5.5v1.616l6.871 1.832a.5.5 0 0 0 .258 0L15 6.116V4.5a.5.5 0 0 0-.5-.5h-13zM5 2.5A1.5 1.5 0 0 1 6.5 1h3A1.5 1.5 0 0 1 11 2.5V3h-1v-.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5V3H5v-.5z"/>
                            </svg>
                            <span class="sr-only">Emploi :</span>
                            {{ $profileUser->currentOccupation()->title }} chez {{ $profileUser->currentOccupation()->company }}
                        </li>
                    @endif

                    @unless(is_null($profileUser->location))
                        <li class="d-flex align-items-center p-3 border-bottom">
                            <svg class="bi bi-geo flex-shrink-0 mr-3" width="1em" height="1em" viewBox="0 0 16 16"
                                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path d="M11 4a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                <path d="M7.5 4h1v9a.5.5 0 0 1-1 0V4z"/>
                                <path fill-rule="evenodd"
                                      d="M6.489 12.095a.5.5 0 0 1-.383.594c-.565.123-1.003.292-1.286.472-.302.192-.32.321-.32.339 0 .013.005.085.146.21.14.124.372.26.701.382.655.246 1.593.408 2.653.408s1.998-.162 2.653-.408c.329-.123.56-.258.701-.382.14-.125.146-.197.146-.21 0-.018-.018-.147-.32-.339-.283-.18-.721-.35-1.286-.472a.5.5 0 1 1 .212-.977c.63.137 1.193.34 1.61.606.4.253.784.645.784 1.182 0 .402-.219.724-.483.958-.264.235-.618.423-1.013.57-.793.298-1.855.472-3.004.472s-2.21-.174-3.004-.471c-.395-.148-.749-.336-1.013-.571-.264-.234-.483-.556-.483-.958 0-.537.384-.929.783-1.182.418-.266.98-.47 1.611-.606a.5.5 0 0 1 .595.383z"/>
                            </svg>
                            <span class="sr-only">Lieu :</span>
                            {{ $profileUser->location->municipality }}, {{ $profileUser->location->country }}
                        </li>
                    @endunless

                    @unless(is_null($profileUser->flight_hours))
                        <li class="d-flex align-items-center p-3 border-bottom">
                            <svg class="bi bi-stopwatch flex-shrink-0 mr-3" width="1em" height="1em" viewBox="0 0 16 16"
                                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M8 15A6 6 0 1 0 8 3a6 6 0 0 0 0 12zm0 1A7 7 0 1 0 8 2a7 7 0 0 0 0 14z"/>
                                <path fill-rule="evenodd"
                                      d="M8 4.5a.5.5 0 0 1 .5.5v4a.5.5 0 0 1-.5.5H4.5a.5.5 0 0 1 0-1h3V5a.5.5 0 0 1 .5-.5zM5.5.5A.5.5 0 0 1 6 0h4a.5.5 0 0 1 0 1H6a.5.5 0 0 1-.5-.5z"/>
                                <path d="M7 1h2v2H7V1z"/>
                            </svg>
                            {{ $profileUser->flight_hours }} heures de vol
                        </li>
                    @endunless

                    @unless(is_null($profileUser->phone))
                        <li class="d-flex align-items-center p-3 border-bottom">
                            <svg class="bi bi-phone flex-shrink-0 mr-3" width="1em" height="1em" viewBox="0 0 16 16"
                                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M11 1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H5z"/>
                                <path fill-rule="evenodd" d="M8 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                            </svg>
                            <span class="sr-only">Téléphone :</span>
                            {{ $profileUser->phone->formatInternational() }}
                        </li>
                    @endunless

                    <li class="d-flex align-items-center p-3">
                        <svg class="bi bi-envelope flex-shrink-0 mr-3" width="1em" height="1em" viewBox="0 0 16 16"
                             fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd"
                                  d="M14 3H2a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4a1 1 0 0 0-1-1zM2 2a2 2 0 0 0-2 2v8a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V4a2 2 0 0 0-2-2H2z"/>
                            <path
                                d="M.05 3.555C.017 3.698 0 3.847 0 4v.697l5.803 3.546L0 11.801V12c0 .306.069.596.192.856l6.57-4.027L8 9.586l1.239-.757 6.57 4.027c.122-.26.191-.55.191-.856v-.2l-5.803-3.557L16 4.697V4c0-.153-.017-.302-.05-.445L8 8.414.05 3.555z"/>
                        </svg>
                        <span class="sr-only">Email :</span>
                        {{ $profileUser->email }}
                    </li>
                </ul>
            </div>

            <div class="col-md-8 col-lg-7">
                <ul class="nav nav-pills nav-justified mb-4" role="tablist">
                    <li class="nav-item" role="presentation">
                        <a class="nav-link active" id="profile-tab" data-toggle="pill" href="#profile" role="tab"
                           aria-controls="home" aria-selected="true">
                            Profil
                        </a>
                    </li>
                    <li class="nav-item" role="presentation">
                        <a class="nav-link" id="activity-tab" data-toggle="pill" href="#activity" role="tab"
                           aria-controls="profile" aria-selected="false">
                            Activité
                        </a>
                    </li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane fade show active" id="profile" role="tabpanel" aria-labelledby="profile-tab">
                        @unless(is_null($profileUser->bio))
                            <h2 class="h3 font-weight-bold mb-3">Biographie</h2>

                            <p class="mb-5">{{ $profileUser->bio }}</p>
                        @endunless

                        @unless($profileUser->experience->isEmpty())
                            <h2 class="h3 font-weight-bold mb-3">Expérience Professionelle</h2>

                            @foreach($profileUser->experience as $occupation)
                                @include('profiles._occupation')
                            @endforeach
                        @endunless

                        @unless($profileUser->education->isEmpty())
                            <h2 class="h3 font-weight-bold mb-3">Éducation</h2>

                            @foreach($profileUser->education as $course)
                                @include('profiles._course')
                            @endforeach
                        @endunless
                    </div>

                    <div class="tab-pane fade" id="activity" role="tabpanel" aria-labelledby="activity-tab">
                        @forelse($activities as $date => $activity)
                            <h2 class="pb-2 border-bottom mb-4">{{ $date }}</h2>
                            @foreach($activity as $record)
                                @if(View::exists("profiles.activities.{$record->type}"))
                                    @include("profiles.activities.{$record->type}", ['activity' => $record])
                                @endif
                            @endforeach
                        @empty
                            <p>Cet utilisateur n'a pas encore d'activité.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
