@if(Auth::user()->hasVerifiedEmail())
    <a href="{{ route('threads.create', ['channel_id' => optional(Route::input('channel'))->id]) }}"
       class="d-sm-none btn btn-lg btn-success rounded-circle lh-1 p-2 ml-2 ml-sm-0">
        <svg class="bi bi-plus" width="1em" height="1em" viewBox="0 0 16 16" fill="currentColor"
             xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M8 3.5a.5.5 0 01.5.5v4a.5.5 0 01-.5.5H4a.5.5 0 010-1h3.5V4a.5.5 0 01.5-.5z"
                  clip-rule="evenodd"/>
            <path fill-rule="evenodd" d="M7.5 8a.5.5 0 01.5-.5h4a.5.5 0 010 1H8.5V12a.5.5 0 01-1 0V8z"
                  clip-rule="evenodd"/>
        </svg>
    </a>

    <a href="{{ route('threads.create', ['channel_id' => optional(Route::input('channel'))->id]) }}"
       class="d-none d-sm-block btn btn-success mb-3">
        Nouvelle discussion
    </a>
@else
    <p class="alert alert-warning text-center p-1">Confirme ton adresse e-mail pour participer</p>
@endif
