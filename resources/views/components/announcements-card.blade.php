<section aria-labelledby="announcements-title" {{ $attributes }}>
    <x-card>
        <h2 class="text-base font-medium text-gray-900" id="announcements-title">Actualités</h2>

        <div class="flow-root mt-6">
            <x-stacked-list>
                <x-content-link href="#">
                    <x-slot name="title">Lancement site public</x-slot>

                    Cum qui rem deleniti. Suscipit in dolor veritatis sequi aut. Vero ut earum quis deleniti. Ut a sunt
                    eum cum ut repudiandae possimus. Nihil ex tempora neque cum consectetur dolores.
                </x-content-link>

                <x-content-link href="#">
                    <x-slot name="title">Réunion de Bureau - Mai 2022</x-slot>

                    Alias inventore ut autem optio voluptas et repellendus. Facere totam quaerat quam quo laudantium
                    cumque eaque excepturi vel. Accusamus maxime ipsam reprehenderit rerum id repellendus rerum. Culpa
                    cum vel natus. Est sit autem mollitia.</p>
                </x-content-link>

                <x-content-link href="#">
                    <x-slot name="title">Séances cinéma Top Gun Maverick</x-slot>

                    Tenetur libero voluptatem rerum occaecati qui est molestiae exercitationem. Voluptate quisquam iure
                    assumenda consequatur ex et recusandae. Alias consectetur voluptatibus. Accusamus a ab dicta et.
                    Consequatur quis dignissimos voluptatem nisi.</p>
                </x-content-link>
            </x-stacked-list>
        </div>

        <div class="mt-6">
            <x-button-white href="#">Tout voir</x-button-white>
        </div>
    </x-card>
</section>
