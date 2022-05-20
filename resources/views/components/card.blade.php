<div {{ $attributes->class(['bg-white shadow sm:rounded-lg divide-y divide-gray-200']) }}>
    @isset($header)
        <div class="px-4 py-5 sm:px-6">
            {{ $header }}
        </div>
    @endisset

    <div class="px-4 py-5 sm:p-6">
        {{ $slot }}
    </div>

    @isset($footer)
        <div class="px-4 py-4 sm:px-6">
            {{ $footer }}
        </div>
    @endisset
</div>
