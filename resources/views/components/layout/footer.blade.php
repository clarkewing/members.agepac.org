<footer class="bg-gray-100">
    <div class="max-w-7xl mx-auto py-10 px-4 border-t border-gray-200 overflow-hidden sm:px-6 lg:px-8">
        <nav class="-mx-5 -my-2 flex flex-wrap justify-center" aria-label="Footer">
            <x-footer-link href="https://agepac.org/association">
                À propos
            </x-footer-link>

            <x-footer-link href="https://agepac.org/press">
                Presse & médias
            </x-footer-link>

            <x-footer-link href="https://agepac.org/contact">
                Contactez-nous
            </x-footer-link>

            <x-footer-link href="https://agepac.org/privacy">
                Confidentialité
            </x-footer-link>

            <x-footer-link href="https://agepac.org/terms">
                Conditions d’utilisation
            </x-footer-link>
        </nav>
        <div class="mt-8 flex justify-center space-x-6">
            <x-footer-social-link href="https://www.linkedin.com/company/agepac/" icon="fab-linkedin">
                LinkedIn
            </x-footer-social-link>

            <x-footer-social-link href="https://www.instagram.com/flyagepac/" icon="fab-instagram">
                Instagram
            </x-footer-social-link>

            <x-footer-social-link href="https://twitter.com/agepac" icon="fab-twitter">
                Twitter
            </x-footer-social-link>

            <x-footer-social-link href="https://github.com/clarkewing/agepac.org-members" icon="fab-github">
                GitHub
            </x-footer-social-link>
        </div>
        <p class="mt-8 text-center text-sm text-gray-400">
            &copy; {{ now()->year }} AGEPAC. Tous droits réservés.
        </p>
    </div>
</footer>
