<div {{ $attributes->class('flex') }}>
    <h4 class="flex-grow leading-6 font-medium text-gray-900">
        {{ $title ?? $slot }}
    </h4>
    @isset($actions)
        <span class="ml-4 flex-shrink-0">
            {{ $actions }}
        </span>
    @endisset
</div>
