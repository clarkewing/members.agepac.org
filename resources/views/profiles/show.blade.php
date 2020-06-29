@extends('layouts.app')

@section('content')
    <div class="container text-primary bg-white mt-n4 py-4 border border-top-0 rounded-bottom">
        <div class="row">
            <div class="col-md-4 col-lg-3 offset-md-0 offset-lg-1">
                <profile.avatar classes="rounded-circle w-50 mx-auto mb-3"
                                :data="{{ json_encode(Arr::only($profile->toArray(), ['avatar_path'])) }}"
                ></profile.avatar>

                <h1 class="h2 font-weight-bold text-center">
                    {{ $profile->name }}
                </h1>

                <h5 class="text-center">
                    {{ $profile->class }}
                </h5>

                <h6 class="text-center mb-4">
                    Réputation : {{ $profile->reputation }} XP
                </h6>

                <ul class="list-unstyled small font-weight-bold mx-4 mx-md-0 mb-5">
                    @if($profile->hasOccupation())
                        <li class="d-flex align-items-center p-3 border-bottom">
                            <svg class="bi bi-briefcase flex-shrink-0 mr-3" width="1em" height="1em" viewBox="0 0 16 16"
                                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M0 12.5A1.5 1.5 0 0 0 1.5 14h13a1.5 1.5 0 0 0 1.5-1.5v-6h-1v6a.5.5 0 0 1-.5.5h-13a.5.5 0 0 1-.5-.5v-6H0v6z"/>
                                <path fill-rule="evenodd"
                                      d="M0 4.5A1.5 1.5 0 0 1 1.5 3h13A1.5 1.5 0 0 1 16 4.5v2.384l-7.614 2.03a1.5 1.5 0 0 1-.772 0L0 6.884V4.5zM1.5 4a.5.5 0 0 0-.5.5v1.616l6.871 1.832a.5.5 0 0 0 .258 0L15 6.116V4.5a.5.5 0 0 0-.5-.5h-13zM5 2.5A1.5 1.5 0 0 1 6.5 1h3A1.5 1.5 0 0 1 11 2.5V3h-1v-.5a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5V3H5v-.5z"/>
                            </svg>
                            <span class="sr-only">Emploi :</span>
                            {{ $profile->currentOccupation()->title }} chez {{ $profile->currentOccupation()->company }}
                        </li>
                    @endif

                    @if(Auth::user()->is($profile) || ! is_null($profile->location))
                        <profile.location
                            :data="{{ json_encode(Arr::only($profile->toArray(), ['location'])) }}"
                        ></profile.location>
                    @endif

                    @if(Auth::user()->is($profile) || ! is_null($profile->flight_hours))
                        <profile.flight-hours
                            :data="{{ json_encode(Arr::only($profile->toArray(), ['flight_hours'])) }}"
                        ></profile.flight-hours>
                    @endif

                    @unless(is_null($profile->phone))
                        <li class="d-flex align-items-center p-3 border-bottom">
                            <svg class="bi bi-phone flex-shrink-0 mr-3" width="1em" height="1em" viewBox="0 0 16 16"
                                 fill="currentColor" xmlns="http://www.w3.org/2000/svg">
                                <path fill-rule="evenodd"
                                      d="M11 1H5a1 1 0 0 0-1 1v12a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1zM5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2H5z"/>
                                <path fill-rule="evenodd" d="M8 14a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/>
                            </svg>
                            <span class="sr-only">Téléphone :</span>
                            {{ $profile->phone->formatInternational() }}
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
                        {{ $profile->email }}
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

                        @if(Auth::user()->is($profile) || ! is_null($profile->bio))
                            <profile.bio
                                :data="{{ json_encode(Arr::only($profile->toArray(), ['bio'])) }}"
                            ></profile.bio>
                        @endif

                        @if(Auth::user()->is($profile) || ! $profile->experience->isEmpty())
                            <profile.experience
                                :data="{{ json_encode(Arr::only($profile->toArray(), ['experience'])) }}"
                            ></profile.experience>
                        @endif

                        @unless($profile->education->isEmpty())
                            <h2 class="h3 font-weight-bold mb-3">Éducation</h2>

                            @foreach($profile->education as $course)
                                @include('profiles._course')
                            @endforeach
                        @endunless
                    </div>

                    <div class="tab-pane fade" id="activity" role="tabpanel" aria-labelledby="activity-tab">
                        @forelse($activities as $date => $activity)
                            <h5 class="font-weight-bolder mb-3">{{ $date }}</h5>
                            @foreach($activity as $record)
                                @if(View::exists("profiles.activities.{$record->type}"))
                                    @include("profiles.activities.{$record->type}", ['activity' => $record])
                                @endif
                            @endforeach
                        @empty
                            <p class="text-center py-5">Cet utilisateur n'a pas encore d'activité.</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.App.aircrafts = {!! App\Aircraft::all()->toJson() !!};
        window.App.occupationStatuses = @json(App\Occupation::statusStrings());
    </script>
@endpush
