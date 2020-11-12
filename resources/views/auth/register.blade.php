@extends('layouts.form-with-bg')

@push('styles')
    @livewireStyles
@endpush

@push('scripts')
    @livewireScripts
@endpush

@section('card-body')
    <livewire:register />

{{--    <registration-form inline-template :config="{{ json_encode(Arr::only(config('council'), ['courses', 'genders'])) }}">--}}
{{--        <div>--}}
{{--            <h2 class="card-title text-center" v-if="! complete">Inscription</h2>--}}

{{--            <div class="tab-content p-3">--}}

{{--                @include('auth.register._name-tab')--}}

{{--                @include('auth.register._identity-tab')--}}

{{--                @include('auth.register._credentials-tab')--}}

{{--                @include('auth.register._details-tab')--}}

{{--                @include('auth.register._summary-tab')--}}

{{--                @include('auth.register._success-tab')--}}

{{--                @include('auth.register._no-invitation-tab')--}}

{{--                @include('auth.register._server-error-tab')--}}

{{--            </div>--}}
{{--        </div>--}}
{{--    </registration-form>--}}
@endsection
