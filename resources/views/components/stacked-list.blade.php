<ul
    {{ $attributes
        ->merge(['role' => 'list'])
        ->class([
            'divide-y divide-gray-200',
            '-my-5' => ! Str::contains($attributes->get('class'), 'my-'),
        ])
    }}
>
    {{ $slot }}
</ul>
