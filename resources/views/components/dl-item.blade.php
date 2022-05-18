<div {{ $attributes->class('py-3 grid grid-cols-3 gap-4') }}>
    <dt class="text-sm font-medium text-gray-500">
        {{ $title }}
    </dt>
    <dd class="flex text-sm text-gray-900 col-span-2">
        <span class="grow">
            {{ $description ?? $slot }}
        </span>
        @isset($actions)
            <span class="ml-4 shrink-0">
                {{ $actions }}
            </span>
        @endisset
    </dd>
</div>
