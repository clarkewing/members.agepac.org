<div>
    @if(! $verified)
        <h3 class="text-xl font-bold">
            Vérification
        </h3>
        <p class="mt-1 text-sm text-gray-500">
            Afin de confirmer ton identité, nous t’avons envoyé un email contenant un code à usage unique.
        </p>

        <form class="mt-6 space-y-6" wire:submit.prevent="verify">
            <div>
                <x-form.input
                    label="Saisis le code à 6 chiffres"
                    name="token"
                    wire:model.defer="token"
                    inputmode="numeric"
                    pattern="[0-9]*"
                    autocomplete="one-time-code"
                    required
                    autofocus
                />

                <p class="mt-2 text-sm text-gray-500">
                    @if($resent)
                        Le code a été renvoyé à ton adresse email.
                    @else
                        Tu n’as pas reçu de code ?
                        <x-button-text wire:click.prevent="resendToken" wire:loading.attr="disabled" wire:target="resendToken">
                            Le renvoyer
                            <x-loading-indicator class="h-4 w-4 ml-1.5" wire:loading wire:target="resendToken" />
                        </x-button-text>
                    @endif
                </p>
            </div>

            <x-form.submit wire:loading.attr="disabled" wire:target="verify">
                <span>Continuer</span>
                <x-heroicon-s-arrow-right class="ml-2 h-4 w-4" aria-hidden="true" wire:loading.remove wire:target="verify" />
                <x-loading-indicator class="ml-2" wire:loading wire:target="verify" />
            </x-form.submit>
        </form>
    @else
        <h3 class="text-xl font-bold">
            Bonjour {{ $this->user->name }} !
        </h3>
        <p class="mt-1 text-base text-gray-500">
            Voilà {{ $this->user->created_at->longAbsoluteDiffForHumans() }} que tu as rejoint l'AGEPAC !<br>
            Profitons de cette migration pour mettre à jour tes informations.
        </p>

        <form class="mt-6 space-y-6" wire:submit.prevent="saveUser">
            @if($this->mustSetClass)
                <x-form.select
                    label="Cursus"
                    help="Les EPL/S sont ab-initio, les EPL/U sont ceux entrés avec un ATPL théorique, et les EPL/P ceux entrés avec un CPL pratique."
                    name="class_course"
                    wire:model.defer="class_course"
                    :options="config('council.courses')"
                    required
                />

                <x-form.input
                    label="Promotion"
                    help="L’année de promotion correspond à ton année d’entrée dans le cursus EPL."
                    name="class_year"
                    wire:model.defer="class_year"
                    type="number"
                    min="1900" max="2199" step="1"
                    required
                />
            @endif

            <x-form.input
                label="Adresse email"
                help="Tu pourras modifier ton adresse email une fois connecté."
                type="email"
                name="email"
                value="{{ $user->email }}"
                disabled
            />

            <x-form.input
                label="Mot de passe"
                help="Choisis quelque chose de sûr et d’au moins 8 caractères."
                name="password"
                type="password"
                wire:model.defer="password"
                autocomplete="new-password"
                placeholder="Foy=Maison<3"
                required
            />

            <x-form.input
                label="Confirmation"
                name="password_confirmation"
                type="password"
                wire:model.defer="password_confirmation"
                autocomplete="new-password"
                required
            />

            <x-form.input
                label="Date de naissance"
                type="date"
                name="birthdate"
                wire:model.defer="birthdate"
                autocomplete="bday"
                placeholder="JJ/MM/YYYY"
                required
            />

            <x-form.select
                label="Genre"
                name="gender"
                wire:model.defer="gender"
                :options="config('council.genders')"
                autocomplete="sex"
                required
            />

            <x-form.input
                label="Numéro de téléphone"
                help="Promis, on en abusera pas !"
                type="tel"
                name="phone"
                wire:model.defer="phone"
                autocomplete="tel"
                placeholder="+33 6 69 69 69 69"
                pattern="[\d+. -]+"
                required
            />

            <x-form.submit wire:loading.attr="disabled" wire:target="saveUser">
                <span>Valider</span>
                <x-heroicon-s-paper-airplane class="ml-2 h-4 w-4 rotate-45 -translate-y-0.5" aria-hidden="true" wire:loading.remove wire:target="saveUser" />
                <x-loading-indicator class="ml-2" wire:loading wire:target="saveUser" />
            </x-form.submit>
        </form>
    @endif
</div>
