<x-jet-form-section submit="updateProfileInformation">
    <x-slot name="title">
        Informations personnelles
    </x-slot>

    <x-slot name="description">
        On essaiera de te souhaiter un joyeux anniversaire tous les ans.
        Ne nous tape pas dessus si on oublie, c'est que
        <a href="https://www.linkedin.com/in/mrhugo/" target="_blank" title="Mec mega trop chouette" class="text-wedgewood-500 hover:underline">Hugo</a>
        a oublié de coder la fonctionnalité !
    </x-slot>

    <x-slot name="form">
        <div class="grid grid-cols-6 gap-6">
            @if (Laravel\Jetstream\Jetstream::managesProfilePhotos())
                <div class="col-span-6">
                    <x-profile-photo-input/>
                </div>
            @endif

            <div class="col-span-6 sm:col-span-3">
                <x-form.input
                    label="Prénom"
                    name="first_name"
                    wire:model.defer="state.first_name"
                    autocomplete="given-name"
                    placeholder="Marc"
                    required
                />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <x-form.input
                    label="Nom"
                    name="last_name"
                    wire:model.defer="state.last_name"
                    autocomplete="family-name"
                    placeholder="Houalla"
                    required
                />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <x-form.select
                    label="Genre"
                    name="gender"
                    wire:model.defer="state.gender"
                    :options="config('council.genders')"
                    autocomplete="sex"
                    required
                />
            </div>

            <div class="col-span-6 sm:col-span-3">
                <x-form.input
                    label="Date de naissance"
                    type="date"
                    name="birthdate"
                    wire:model.defer="state.birthdate"
                    autocomplete="bday"
                    placeholder="JJ/MM/YYYY"
                    required
                />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-form.input
                    label="Adresse email"
                    help="Tu devras revérifier ton adresse email si tu la modifies."
                    type="email"
                    name="email"
                    wire:model.defer="state.email"
                    autocomplete="email"
                    placeholder="choucroute_loveur@example.com"
                    required
                />
            </div>

            <div class="col-span-6 sm:col-span-4">
                <x-form.input
                    label="Numéro de téléphone"
                    type="tel"
                    name="phone"
                    wire:model.defer="state.phone"
                    autocomplete="tel"
                    placeholder="+33 6 69 69 69 69"
                    pattern="[\d+. -]+"
                    required
                />
            </div>
        </div>
    </x-slot>

    <x-slot name="actions">
        <x-jet-action-message class="mr-3" on="saved">
            Sauvegardé !
        </x-jet-action-message>

        <x-button.primary
            type="submit"
            class="w-auto"
            wire:loading.attr="disabled"
            wire:target="photo"
        >
            Sauvegarder
        </x-button.primary>
    </x-slot>
</x-jet-form-section>
