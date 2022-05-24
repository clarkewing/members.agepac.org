@if(Auth::user()->subscribed('default'))
    <h5 class="font-weight-bold pb-2 border-bottom mb-4">Adhésion en cours</h5>

    <div class="px-lg-4 mb-5">
        <h5>
            @if(Auth::user()->subscribedToPrice(config('council.plans.agepac'), 'default'))
                AGEPAC (30,00 € / an)
            @elseif(Auth::user()->subscribedToPrice(config('council.plans.agepac+alumni'), 'default'))
                AGEPAC + ENAC Alumni (60,00 € / an)
            @endif
        </h5>
    </div>

@else
    <subscription-plans inline-template>
        <div>
            <div class="card-deck px-sm-3 px-md-0 px-lg-5 mb-5 text-center">
                <div class="card pricing-card border-success shadow">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">Cotisation AGEPAC</h4>
                    </div>
                    <div class="card-body">
                        <h1 class="card-title">
                            30€ <small class="text-muted">/ an</small>
                        </h1>

                        <h4 class="text-uppercase text-success font-weight-bold mt-n1">Le plus populaire</h4>

                        <ul class="list-unstyled mt-3 mb-0">
                            <li>
                                📣
                                Accède à un forum dynamique
                            </li>
                            <li>
                                ✈
                                Des offres d'emploi exclusives
                            </li>
                            <li>
                                📖
                                Annuaire de plus de 600 EPL
                            </li>
                            <li>
                                🍻
                                <del>Apéros</del>
                                Évènements fréquents
                            </li>
                            <li>
                                ☎
                                Helpline joignable H24
                            </li>
                        </ul>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        @if(! Auth::user()->hasDefaultPaymentMethod())
                            <span class="d-block text-muted px-2 py-1 border border-muted rounded">
                            Ajoute un moyen de paiement pour pouvoir adhérer
                        </span>
                        @else
                            <button type="button" @click="startSubscription('agepac')"
                                    class="btn btn-lg btn-block btn-success stretched-link">
                                Adhérer !
                            </button>
                        @endif
                    </div>
                </div>

                <div class="card pricing-card border-info shadow">
                    <div class="card-header">
                        <h4 class="my-0 font-weight-normal">AGEPAC + ENAC Alumni</h4>
                    </div>
                    <div class="card-body">
                        <h1 class="card-title">
                            60€ <small class="text-muted">/ an</small>
                        </h1>

                        <h4 class="text-uppercase text-info font-weight-bold mt-n1">Le plus complet</h4>

                        <ul class="list-unstyled mt-3 mb-4">
                            <li>Tous les avantages de l'AGEPAC</li>
                            <li class="font-weight-bolder">
                                +
                                <a href="https://www.alumni.enac.fr/fr" target="_blank"
                                   class="position-relative" style="z-index: 2;">
                                    avantages ENAC Alumni
                                </a>
                            </li>
                        </ul>

                        <p>Contre 90€ si tu cotises séparemment !</p>
                    </div>
                    <div class="card-footer bg-transparent border-0">
                        @if(! Auth::user()->hasDefaultPaymentMethod())
                            <span class="d-block text-muted px-2 py-1 border border-muted rounded">
                            Ajoute un moyen de paiement pour pouvoir adhérer
                        </span>
                        @else
                            <button type="button" @click="startSubscription('agepac+alumni')"
                                    class="btn btn-lg btn-block btn-outline-info stretched-link">
                                Adhérer !
                            </button>
                        @endif
                    </div>
                </div>
            </div>

            <div ref="createSubModal" class="modal fade" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                    <div class="modal-content">
                        <div class="modal-header flex-column align-items-center">
                            <h2 class="modal-title mb-2 font-weight-bold">Adhésion</h2>
                            <h4 class="mb-1" v-text="plans[desiredPlan].name"></h4>
                            <h5 class="h6 text-muted mb-1">
                                @{{ toEuros(plans[desiredPlan].price) }} / an
                            </h5>
                        </div>

                        <div class="modal-body">
                            <div class="text-center">
                                <h5>Merci de ton intérêt !</h5>
                                <p>
                                    On t'embête rapidement avec quelques conditions à accepter avant de continuer...<br>
                                    OUI ON SAIT, c'est chiant. Mais c'est obligatoire pour qu'on soit en règle auprès de
                                    nos différentes autorités de tutelle (Préfecture, État, Commission Européenne,
                                    toussa toussa).
                                </p>
                            </div>

                            <div class="accordion mx-n3 mb-n3" id="subscriptionTerms">
                                <div class="card rounded-0 border-left-0 border-right-0">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <svg class="bi bi-check2 mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                                                 fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                                 v-if="acceptedTerms1">
                                                <path fill-rule="evenodd"
                                                      d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                                            </svg>
                                            <button class="btn btn-link p-0" type="button"
                                                    data-toggle="collapse" data-target="#subscriptionTerms1"
                                                    v-if="acceptedTerms1">
                                                Charte de bonne conduite
                                            </button>
                                            <span v-else>
                                            Charte de bonne conduite
                                        </span>
                                        </h6>
                                    </div>

                                    <div id="subscriptionTerms1" class="collapse show" data-parent="#subscriptionTerms">
                                        <div class="card-body small">
                                            <p class="mb-2">
                                                Je m’engage à respecter les règles suivantes :
                                            </p>
                                            <ul>
                                                <li>
                                                    Je me conduis de manière responsable et je véhicule une image
                                                    positive de l’Association ;
                                                </li>
                                                <li>
                                                    Je contribue à mon échelle au développement du réseau, à
                                                    l'élaboration de nouveaux projets et à l'apport de contenu sur
                                                    la plateforme numérique ;
                                                </li>
                                                <li>
                                                    Je m’engage à ne pas chercher à tirer profit du statut, du poste, ou
                                                    des relations d’autres adhérents sans leur accord ;
                                                </li>
                                                <li>
                                                    Je m'engage à ne pas détourner les avantages et/ou les offres,
                                                    notamment d'emplois, proposés par l'AGEPAC pour en tirer un profit
                                                    personnel au détriment des autres adhérents ;
                                                </li>
                                                <li>
                                                    Je m'engage à ne pas porter atteinte sciemment par mon attitude, mes
                                                    propos, ou mes actes, à la profession de pilote et plus généralement
                                                    au milieu aéronautique ;
                                                </li>
                                                <li>
                                                    Je m'engage à respecter la confidentialité de l’ensemble des
                                                    documents et informations auxquels j'ai accès via le forum et
                                                    l'Association AGEPAC ;
                                                </li>
                                                <li>
                                                    Le nom de l’Association « AGEPAC » ne pourra en aucun cas être
                                                    utilisé à titre personnel et/ou individuel pour son propre bénéfice
                                                    ou au bénéfice d’un tiers sans l’accord du Conseil d'Administration
                                                    ;
                                                </li>
                                                <li>
                                                    Les membres du Bureau sont responsables de la communication externe
                                                    (presse, TV, radio, partenaires, ...). Par conséquent, tout membre
                                                    de l'Association qui souhaitera s'exprimer officiellement au nom de
                                                    l'Association devra en informer le Bureau et ne devra communiquer
                                                    que sur des informations officiellement validées ;
                                                </li>
                                            </ul>
                                            <p>
                                                Je déclare avoir lu et compris le présent document et m’engage à m’y
                                                conformer sans réserve. En cas de non-respect de l’un ou de plusieurs de
                                                ces articles, je m’expose à des sanctions (avertissement, exclusion
                                                provisoire, exclusion définitive) définies et notifiées par une
                                                commission de discipline mandatée par les membres du Conseil
                                                d'Administration.
                                            </p>

                                            <button type="button" class="btn btn-primary d-block mx-auto"
                                                    @click="acceptTerms(1)">
                                                J'accepte
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card rounded-0 border-left-0 border-right-0">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <svg class="bi bi-check2 mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                                                 fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                                 v-if="acceptedTerms2">
                                                <path fill-rule="evenodd"
                                                      d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                                            </svg>
                                            <button class="btn btn-link p-0" type="button"
                                                    data-toggle="collapse" data-target="#subscriptionTerms2"
                                                    v-if="acceptedTerms2">
                                                Politique de cookies
                                            </button>
                                            <span v-else>
                                            Politique de cookies
                                        </span>
                                        </h6>
                                    </div>

                                    <div id="subscriptionTerms2" class="collapse" data-parent="#subscriptionTerms">
                                        <div class="card-body small">
                                            <p>
                                                Pour assurer le bon fonctionnement de ce site, nous devons parfois
                                                enregistrer de petits fichiers de données sur l'équipement de nos
                                                utilisateurs. La plupart des grands sites web font de même.
                                            </p>

                                            <h6>Qu'est-ce qu'un cookie ?</h6>

                                            <p>
                                                Un cookie est un petit fichier texte que l'on stocke sur ton device
                                                lorsque tu consultes notre site. Il nous permet de mémoriser tes actions
                                                et préférences (nom d'utilisateur, langue, dark mode...) pendant une
                                                période déterminée, pour que tu n'aies pas à ressaisir ces informations
                                                à chaque fois que tu consultes le site ou que tu navigues d'une page à
                                                une autre.
                                            </p>

                                            <h6>Comment utilisons-nous les cookies ?</h6>

                                            <p class="mb-2">
                                                Certaines de nos pages utilisent des cookies afin de :
                                            </p>

                                            <ul>
                                                <li>
                                                    T'identifier, et ainsi t'éviter d'avoir à te reconnecter à chaque
                                                    fois que tu visites notre site ;
                                                </li>
                                                <li>
                                                    Mémoriser tes préférences d'affichage, comme le thème ou le dark
                                                    mode ;
                                                </li>
                                                <li>
                                                    Collecter des données sur tes habitudes de navigation sur notre site
                                                    via Google Analytics. Ceci nous permet de mieux comprendre le
                                                    comportement des utilisateurs pour continuellement améliorer
                                                    l'expérience du
                                                    site.
                                                </li>
                                            </ul>

                                            <p>
                                                Les informations contenues dans les cookies ne visent pas à t'identifier
                                                personnellement et nous en contrôlons pleinement les données. Ces
                                                cookies ne sont jamais utilisés à d'autres fins que celles indiquées
                                                ici.
                                            </p>

                                            <h6>Comment contrôler les cookies</h6>

                                            <p>
                                                Tu peux contrôler et/ou supprimer des cookies comme bon te semble. Pour
                                                en savoir plus, consulte
                                                <a href="https://aboutcookies.org">aboutcookies.org</a>.<br>
                                                Tu as la possibilité de supprimer tous les cookies déjà stockés sur ton
                                                device et de configurer la plupart des navigateurs pour qu'ils les
                                                bloquent. Toutefois, dans ce cas, certains services et fonctionnalités
                                                risquent de ne pas être accessibles.
                                            </p>

                                            <button type="button" class="btn btn-primary d-block mx-auto"
                                                    @click="acceptTerms(2)">
                                                J'accepte
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card rounded-0 border-left-0 border-right-0">
                                    <div class="card-header">
                                        <h6 class="mb-0">
                                            <svg class="bi bi-check2 mr-1" width="1em" height="1em" viewBox="0 0 16 16"
                                                 fill="currentColor" xmlns="http://www.w3.org/2000/svg"
                                                 v-if="acceptedTerms3">
                                                <path fill-rule="evenodd"
                                                      d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/>
                                            </svg>
                                            <button class="btn btn-link p-0" type="button"
                                                    data-toggle="collapse" data-target="#subscriptionTerms3"
                                                    v-if="acceptedTerms3">
                                                Conditions de paiement
                                            </button>
                                            <span v-else>
                                            Conditions de paiement
                                        </span>
                                        </h6>
                                    </div>

                                    <div id="subscriptionTerms3" class="collapse" data-parent="#subscriptionTerms">
                                        <div class="card-body small">
                                            <p>
                                                Ton moyen de paiement par défaut sera prélevé de @{{
                                                toEuros(plans[desiredPlan].price) }} par an.<br>
                                                Le renouvellement automatique est activé par défaut. Tu as la
                                                possibilité
                                                de le désactiver à tout moment, cependant toute année de cotisation
                                                commencée est dûe en son intégralité et les remboursements au prorata ne
                                                sont pas prévus.
                                            </p>

                                            <button type="button" class="btn btn-primary d-block mx-auto"
                                                    @click="acceptTerms(3)">
                                                J'accepte
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                <div class="card rounded-0 border-left-0 border-right-0">
                                    <div id="subscriptionTerms4" class="collapse" data-parent="#subscriptionTerms">
                                        <form class="card-body" method="post"
                                              action="{{ route('subscription.store') }}">
                                            @csrf
                                            <input type="hidden" name="plan" v-model="desiredPlan">

                                            <button type="submit" class="btn btn-lg btn-success d-block mx-auto">
                                                Je cotise !
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </subscription-plans>
@endif
