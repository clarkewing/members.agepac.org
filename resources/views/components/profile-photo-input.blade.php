<div
    x-data="{photoName: null, photoPreview: null}"
    class="space-y-1"
>
    <label for="photo" class="block text-sm font-medium text-gray-700">
        Photo
    </label>

    <div class="space-y-2">
        <input
            type="file"
            class="hidden"
            id="photo"
            name="photo"
            wire:model="photo"
            wire:loading.attr="disabled"
            wire:target="photo"
            @error('photo')
                aria-invalid="true"
                aria-describedby="photo-error"
            @enderror
            x-ref="photo"
            x-on:change="
                photoName = $refs.photo.files[0].name;
                const reader = new FileReader();
                reader.onload = (e) => {
                    photoPreview = e.target.result;
                };
                reader.readAsDataURL($refs.photo.files[0]);
            "
        />

        <div class="mt-1 flex items-center">
            <span class="inline-block h-12 w-12 overflow-hidden rounded-full bg-gray-100">
                <!-- Current Profile Photo -->
                <div class="w-full h-full" x-show="! photoPreview">
                    <img
                            class="h-full w-full object-cover"
                            src="{{ $this->user->profile_photo_url }}"
                            alt="{{ $this->user->name }}"
                    />
                </div>

                <!-- New Profile Photo Preview -->
                <div class="w-full h-full" x-show="photoPreview">
                    <span
                            class="block w-full h-full bg-cover bg-no-repeat bg-center"
                            x-bind:style="'background-image: url(\'' + photoPreview + '\');'"
                    ></span>
                </div>
            </span>

            <x-button.white
                    class="w-auto ml-5"
                    x-on:click.prevent="$refs.photo.click()"
                    wire:loading.attr="disabled"
                    wire:target="photo"
            >
                Modifier
            </x-button.white>

            @if ($this->user->profile_photo_path)
                <x-button.white
                        class="w-auto ml-2"
                        wire:click="deleteProfilePhoto"
                        wire:loading.attr="disabled"
                        wire:target="photo"
                >
                    Supprimer
                </x-button.white>
            @endif
        </div>

        @error('photo')
            <p
                class="text-sm text-red-600"
                id="photo-error"
            >
                {{ $message }}
            </p>
        @enderror
    </div>
</div>
