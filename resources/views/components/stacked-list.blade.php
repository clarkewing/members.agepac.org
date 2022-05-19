<ul {{ $attributes->merge([
    'role' => 'list',
    'class' => '-my-5 divide-y divide-gray-200',
]) }}>
    {{ $slot }}
</ul>
