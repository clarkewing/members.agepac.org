<div {{ $attributes->class('flex') }}>
    <h4 class="grow leading-6 font-medium text-gray-900">
        {{ $title ?? $slot }}
    </h4>
    @isset($actions)
        <span class="ml-4 shrink-0">
            {{ $actions }}
        </span>
    @endisset
</div>
