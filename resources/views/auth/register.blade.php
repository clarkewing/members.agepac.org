@push('styles')
    <style>
        #helloContainer.play .path-1,
        #helloContainer.play .path-2,
        #helloContainer.play .path-3 {
            stroke-dashoffset: 0;
        }

        #helloContainer.play .path-4 {
            stroke-width: 18px;
        }

        #helloContainer .path {
            fill: none;
            stroke: #000A33;
            stroke-width: 18px;
            stroke-linecap: round;
            stroke-linejoin: round;
        }

        #helloContainer .path-1 {
            stroke-dasharray: 1850 2000;
            stroke-dashoffset: 1851;
            transition: 3s linear;
        }

        #helloContainer .path-2 {
            stroke-dasharray: 260 1000;
            stroke-dashoffset: 261;
            transition: .7s linear 3.2s;
        }

        #helloContainer .path-3 {
            stroke-dasharray: 100 1000;
            stroke-dashoffset: 101;
            transition: .6s linear 4s;
        }

        #helloContainer .path-4 {
            stroke-width: 0;
            transition: 0.1s linear 4.8s;
        }
    </style>
@endpush

@push('scripts')
    <script>
        Livewire.on('registration-started', () => {
            document.getElementById('registrationHeader').classList.add('hidden');
        })

        Livewire.on('success', () => {
            setTimeout(() => document.getElementById('helloContainer').classList.add('play'), 250);
        })
    </script>
@endpush

<x-auth-form-layout>
    <x-slot name="header">
        <x-application-mark class="h-10 w-auto" />
        <div id="registrationHeader">
            <h2 class="mt-6 text-3xl font-extrabold text-gray-900">Rejoins l’AGEPAC</h2>
            <p class="mt-2 text-sm text-gray-600">
                Ou
                <a
                    href="{{ route('login') }}"
                    class="font-medium text-wedgewood-600 hover:text-wedgewood-500"
                >
                    connecte-toi avec ton compte existant
                </a>
            </p>
        </div>
    </x-slot>

    <livewire:register />
</x-auth-form-layout>
