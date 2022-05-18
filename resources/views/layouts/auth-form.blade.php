<x-base-layout class="h-full">
    <div class="min-h-full lg:max-h-full flex">
        <div class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:flex-none lg:px-20 xl:px-24">
            <div class="mx-auto flex flex-col justify-center h-full w-full max-w-sm lg:w-96">
{{--                <div {{ $header->attributes }}>--}}
                <div>
                    {{ $header }}
                </div>

                <div class="mt-8 -mx-1 px-1 pb-1 lg:overflow-y-auto lg:scroll-smooth">
                    {{ $slot }}
                </div>
            </div>
        </div>
        <div class="hidden lg:block relative w-0 flex-1">
            <img
                class="absolute inset-0 h-full w-full object-cover"
                src="{{ asset('background.jpg') }}"
                alt=""
            />
        </div>
    </div>
</x-base-layout>
