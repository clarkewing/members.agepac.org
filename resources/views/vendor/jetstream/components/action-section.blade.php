<div {{ $attributes }}>
    <div class="shadow sm:overflow-hidden sm:rounded-md">
        <div class="space-y-6 bg-white py-6 px-4 sm:p-6">
            <div>
                <h3 class="text-lg font-medium leading-6 text-gray-900">{{ $title }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ $description }}</p>
            </div>

            {{ $content }}
        </div>
    </div>
</div>
