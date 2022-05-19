<x-_button
    {{ $attributes->merge(['class' => 'w-full flex items-center justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-wedgewood-500']) }}
>
    {{ $slot }}
</x-_button>
