<button
    class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-wedgewood-700 bg-wedgewood-100 hover:bg-wedgewood-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-wedgewood-500"
    {{ $attributes->merge(['type' => 'button']) }}
>
    {{ $slot }}
</button>
