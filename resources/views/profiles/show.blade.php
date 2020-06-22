@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-3 offset-md-0">
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

                <ul class="list-unstyled small font-weight-bold mx-4 mx-md-0">
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

            <div class="col-md-9">
                @unless(is_null($profileUser->bio))
                    <h2 class="h3 font-weight-bold">Biographie</h2>
                    <p class="mb-5">{{ $profileUser->bio }}</p>
                @endunless

                @unless($profileUser->experience->isEmpty())
                    <h2 class="h3 font-weight-bold">Expérience Professionelle</h2>
                    @foreach($profileUser->experience as $occupation)
                        <div class="mb-5">
                            <div class="d-flex">
                                <h5 class="flex-grow-1">
                                    {{ $occupation->title }}
                                </h5>

                                <h6 class="small font-weight-bold bg-success rounded-pill px-3 py-1">
                                    {{ $occupation->status() }}
                                </h6>
                            </div>

                            <div class="d-flex">
                                <h6 class="small text-muted mr-4">
                                    <svg class="bi bi-building mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                                         fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                              d="M15.285.089A.5.5 0 0 1 15.5.5v15a.5.5 0 0 1-.5.5h-3a.5.5 0 0 1-.5-.5V14h-1v1.5a.5.5 0 0 1-.5.5H1a.5.5 0 0 1-.5-.5v-6a.5.5 0 0 1 .418-.493l5.582-.93V3.5a.5.5 0 0 1 .324-.468l8-3a.5.5 0 0 1 .46.057zM7.5 3.846V8.5a.5.5 0 0 1-.418.493l-5.582.93V15h8v-1.5a.5.5 0 0 1 .5-.5h2a.5.5 0 0 1 .5.5V15h2V1.222l-7 2.624z"/>
                                        <path fill-rule="evenodd" d="M6.5 15.5v-7h1v7h-1z"/>
                                        <path
                                            d="M2.5 11h1v1h-1v-1zm2 0h1v1h-1v-1zm-2 2h1v1h-1v-1zm2 0h1v1h-1v-1zm6-10h1v1h-1V3zm2 0h1v1h-1V3zm-4 2h1v1h-1V5zm2 0h1v1h-1V5zm2 0h1v1h-1V5zm-2 2h1v1h-1V7zm2 0h1v1h-1V7zm-4 0h1v1h-1V7zm0 2h1v1h-1V9zm2 0h1v1h-1V9zm2 0h1v1h-1V9zm-4 2h1v1h-1v-1zm2 0h1v1h-1v-1zm2 0h1v1h-1v-1z"/>
                                    </svg>
                                    <span class="sr-only">Entreprise :</span>
                                    {{ $occupation->company }}
                                </h6>

                                <h6 class="small text-muted mr-4">
                                    <svg class="bi bi-geo mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                                         fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11 4a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                        <path d="M7.5 4h1v9a.5.5 0 0 1-1 0V4z"/>
                                        <path fill-rule="evenodd"
                                              d="M6.489 12.095a.5.5 0 0 1-.383.594c-.565.123-1.003.292-1.286.472-.302.192-.32.321-.32.339 0 .013.005.085.146.21.14.124.372.26.701.382.655.246 1.593.408 2.653.408s1.998-.162 2.653-.408c.329-.123.56-.258.701-.382.14-.125.146-.197.146-.21 0-.018-.018-.147-.32-.339-.283-.18-.721-.35-1.286-.472a.5.5 0 1 1 .212-.977c.63.137 1.193.34 1.61.606.4.253.784.645.784 1.182 0 .402-.219.724-.483.958-.264.235-.618.423-1.013.57-.793.298-1.855.472-3.004.472s-2.21-.174-3.004-.471c-.395-.148-.749-.336-1.013-.571-.264-.234-.483-.556-.483-.958 0-.537.384-.929.783-1.182.418-.266.98-.47 1.611-.606a.5.5 0 0 1 .595.383z"/>
                                    </svg>
                                    <span class="sr-only">Lieu :</span>
                                    {{ $occupation->location->municipality }}, {{ $occupation->location->country }}
                                </h6>

                                <h6 class="small text-muted ml-auto">
                                    <svg class="bi bi-calendar mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                                         fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                              d="M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1zm1-3a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H2z"/>
                                        <path fill-rule="evenodd"
                                              d="M3.5 0a.5.5 0 0 1 .5.5V1a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 .5-.5zm9 0a.5.5 0 0 1 .5.5V1a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 .5-.5z"/>
                                    </svg>
                                    <span class="sr-only">Dates :</span>
                                    {{ $occupation->start_date->isoFormat('LL') }}
                                    - {{ optional($occupation->end_date)->isoFormat('LL') }}
                                </h6>
                            </div>

                            @unless(is_null($occupation->description))
                                <p>{{ $occupation->description }}</p>
                            @endunless
                        </div>
                    @endforeach
                @endunless

                @unless($profileUser->education->isEmpty())
                    <h2 class="h3 font-weight-bold">Éducation</h2>
                    @foreach($profileUser->education as $course)
                        <div class="mb-5">
                            <h5>{{ $course->title }}</h5>

                            <div class="d-flex">
                                <h6 class="small text-muted mr-4">
                                    <svg class="bi fa-graduation-cap mr-1" width="1em" height="1em" viewBox="0 0 640 512"
                                         fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                              d="M606.72 147.91l-258-79.57c-18.81-5.78-38.62-5.78-57.44 0l-258 79.57C13.38 154.05 0 171.77 0 192.02s13.38 37.97 33.28 44.11l22.64 6.98c-2.46 5.19-4.4 10.62-5.7 16.31C39.53 264.6 32 275.33 32 288.01c0 10.78 5.68 19.85 13.86 25.65L20.33 428.53C18.11 438.52 25.71 448 35.95 448h56.11c10.24 0 17.84-9.48 15.62-19.47L82.14 313.66c8.17-5.8 13.86-14.87 13.86-25.65 0-10.6-5.49-19.54-13.43-25.36 1.13-3.55 2.96-6.67 4.85-9.83l54.87 16.92L128 384c0 35.34 85.96 64 192 64s192-28.65 192-64l-14.28-114.26 109-33.62c19.91-6.14 33.28-23.86 33.28-44.11s-13.38-37.96-33.28-44.1zM462.44 374.47c-59.7 34.2-225.9 33.78-284.87 0l11.3-90.36 102.42 31.59c11.15 3.43 32.24 7.77 57.44 0l102.42-31.59 11.29 90.36zM334.59 269.82c-9.44 2.91-19.75 2.91-29.19 0L154.62 223.3l168.31-31.56c8.69-1.62 14.41-9.98 12.78-18.67-1.62-8.72-10.09-14.36-18.66-12.76l-203.78 38.2c-6.64 1.24-12.8 3.54-18.71 6.27L53.19 192l252.22-77.79c9.44-2.91 19.75-2.91 29.19 0l252.22 77.82-252.23 77.79z"/>
                                    </svg>
                                    <span class="sr-only">École :</span>
                                    {{ $course->school }}
                                </h6>

                                <h6 class="small text-muted mr-4">
                                    <svg class="bi bi-geo mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                                         fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path d="M11 4a3 3 0 1 1-6 0 3 3 0 0 1 6 0z"/>
                                        <path d="M7.5 4h1v9a.5.5 0 0 1-1 0V4z"/>
                                        <path fill-rule="evenodd"
                                              d="M6.489 12.095a.5.5 0 0 1-.383.594c-.565.123-1.003.292-1.286.472-.302.192-.32.321-.32.339 0 .013.005.085.146.21.14.124.372.26.701.382.655.246 1.593.408 2.653.408s1.998-.162 2.653-.408c.329-.123.56-.258.701-.382.14-.125.146-.197.146-.21 0-.018-.018-.147-.32-.339-.283-.18-.721-.35-1.286-.472a.5.5 0 1 1 .212-.977c.63.137 1.193.34 1.61.606.4.253.784.645.784 1.182 0 .402-.219.724-.483.958-.264.235-.618.423-1.013.57-.793.298-1.855.472-3.004.472s-2.21-.174-3.004-.471c-.395-.148-.749-.336-1.013-.571-.264-.234-.483-.556-.483-.958 0-.537.384-.929.783-1.182.418-.266.98-.47 1.611-.606a.5.5 0 0 1 .595.383z"/>
                                    </svg>
                                    <span class="sr-only">Lieu :</span>
                                    {{ $course->location->municipality }}, {{ $course->location->country }}
                                </h6>

                                <h6 class="small text-muted ml-auto">
                                    <svg class="bi bi-calendar mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                                         fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                        <path fill-rule="evenodd"
                                              d="M1 4v10a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V4H1zm1-3a2 2 0 0 0-2 2v11a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V3a2 2 0 0 0-2-2H2z"/>
                                        <path fill-rule="evenodd"
                                              d="M3.5 0a.5.5 0 0 1 .5.5V1a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 .5-.5zm9 0a.5.5 0 0 1 .5.5V1a.5.5 0 0 1-1 0V.5a.5.5 0 0 1 .5-.5z"/>
                                    </svg>
                                    <span class="sr-only">Dates :</span>
                                    {{ $course->start_date->isoFormat('LL') }}
                                    - {{ optional($course->end_date)->isoFormat('LL') }}
                                </h6>
                            </div>

                            @unless(is_null($course->description))
                                <p>{{ $course->description }}</p>
                            @endunless
                        </div>
                    @endforeach
                @endunless

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
@endsection
