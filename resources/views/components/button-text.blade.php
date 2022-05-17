<button
    {{ $attributes->merge([
        'type' => 'button',
        'class' => 'flex items-center justify-center rounded-md font-medium text-wedgewood-600 hover:text-wedgewood-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-wedgewood-500'
    ]) }}
>
    {{ $slot }}
</button>
