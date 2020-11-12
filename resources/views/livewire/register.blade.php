<div>
    <h2 class="card-title text-center">Inscription</h2>

    <div class="tab-content p-3">
        @foreach($steps as $index => $step)
            @include('livewire.register.steps.' . Str::kebab($step['name']), [
                    'active' => $index === $active,
            ])
        @endforeach
    </div>
</div>
