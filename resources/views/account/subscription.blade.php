@extends('account.layout')

@section('section_title')
    Ma cotisation
@endsection

@section('section_content')
    @include('account.subscription.plans')

    @include('account.subscription.payment-methods')

    @if(Auth::user()->subscribed('default'))
        @include('account.subscription.invoices')

        @include('account.subscription.autorenew')
    @endif
@endsection

@push('scripts')
    <script src="https://js.stripe.com/v3/"></script>
@endpush
