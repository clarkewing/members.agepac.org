<form method="POST" action="{{ route('logout') }}" {{ $attributes }}>
    @csrf

    {{ $slot }}
</form>
