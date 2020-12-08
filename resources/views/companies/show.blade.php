@extends('layouts.app')

@section('content')
    <company-view :data="{{ $company->toJson() }}"></company-view>
@endsection

@push('scripts')
    <script>
        window.App.companyTypes = @json(App\Models\Company::typeStrings());
    </script>
@endpush
