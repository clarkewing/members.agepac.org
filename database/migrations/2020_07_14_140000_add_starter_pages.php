<?php

use App\Models\Page;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddStarterPages extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->createHelpPage();

        $this->createContactPage();

        $this->createPrivacyPolicyPage();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        DB::table('pages')->truncate();
    }

    protected function createHelpPage(): void
    {
        Page::forceCreate([
            'title' => 'Aide',
            'path' => 'help',
            'body' => <<<'EOD'
<!-- wp:heading -->
<h2>Besoin d’aide ?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Si tu rencontres des difficultés pour naviguer ou t’inscrire sur le site de l’AGEPAC, n’hésite pas à contacter les membres du Bureau qui sauront t’apporter une solution :<br></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center"} -->
<p style="text-align:center"><a href="mailto:bureau@agepac.org"><strong>bureau@agepac.org</strong></a></p>
<!-- /wp:paragraph -->
EOD,
            'restricted' => false,
        ]);
    }

    protected function createContactPage(): void
    {
        Page::forceCreate([
            'title' => 'Contact',
            'path' => 'contact',
            'body' => <<<'EOD'
<!-- wp:paragraph -->
<p>Pour toute question sur le fonctionnement de notre Association, l’accès au site internet, les contacts presse envoyer un mail à :</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center"} -->
<p style="text-align:center"><a href="mailto:bonjour@agepac.org"><strong>bonjour@agepac.org</strong></a></p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Il est également possible de nous contacter par courrier à l’adresse suivante :</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph {"align":"center"} -->
<p style="text-align:center">AGEPAC<br>École Nationale de l'Aviation Civile<br>7 avenue Edouard Belin<br>31400 Toulouse<br>France</p>
<!-- /wp:paragraph -->
EOD,
            'restricted' => false,
        ]);
    }

    protected function createPrivacyPolicyPage(): void
    {
        Page::forceCreate([
            'title' => 'Politique de Confidentialité',
            'path' => 'policies/privacy',
            'body' => <<<'EOD'
<!-- wp:heading -->
<h2>Introduction</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Dans le cadre de son activité, l’AGEPAC, dont le siège social est situé au 7 avenue Edouard Belin, 31400 Toulouse, est amenée à collecter et à traiter des informations dont certaines sont qualifiées de « données personnelles ».</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>L’AGEPAC attache une grande importance au respect de la vie privée, et n’utilise que des données de manière responsable et confidentielle et dans une finalité précise.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Données personnelles</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Sur le site web agepac.org, il y a 2 types de données susceptibles d’être recueillies :</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Les données transmises directement</strong><br>Ces données sont celles que vous nous transmettez directement, via un formulaire d’inscription ou bien par contact direct par email. Sont obligatoires dans le formulaire d’inscription les champs « prénom », « nom », « promotion », « email », « date de naissance » et « numéro de téléphone ».</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p><strong>Les données collectées automatiquement</strong><br>Lors de vos visites, une fois votre consentement donné, nous pouvons recueillir des informations de type « web analytics » relatives à votre navigation, la durée de votre consultation, votre adresse IP, votre type et version de navigateur. La technologie utilisée est le cookie.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Utilisation des données</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Les données que vous nous transmettez directement sont utilisées dans le but de vous permettre d’utiliser pleinement les fonctionnalités offertes par notre plateforme, vous contacter et/ou dans le cadre de la demande que vous nous faites.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Les données « web analytics » sont collectées anonymement (en enregistrant des adresses IP anonymes) par Google Analytics, et nous permettent de mesurer l'audience de notre site web, les consultations et les éventuelles erreurs afin de constamment améliorer l’expérience des utilisateurs.</p>
<!-- /wp:paragraph -->

<!-- wp:paragraph -->
<p>Ces données sont utilisées par l’AGEPAC, responsable du traitement des données, et ne seront jamais cédées à un tiers ni utilisées à d’autres fins que celles détaillées ci-dessus.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Base légale</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Les données personnelles ne sont collectées qu’après consentement obligatoire de l’utilisateur. Ce consentement est valablement recueilli (boutons et cases à cocher), libre, clair et sans équivoque.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Durée de conservation</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Les données associées à votre compte sur notre site sont conservées tant que votre compte est actif. Toute autre donnée est sauvegardée durant une durée maximale de 3 ans.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Cookies</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Voici la liste des cookies utilisées et leur objectif :</p>
<!-- /wp:paragraph -->

<!-- wp:list -->
<ul><li>Cookies Google Analytics (<a href="https://developers.google.com/analytics/devguides/collection/analyticsjs/cookie-usage">liste exhaustive</a>) : Web analytics</li><li>Cookies permettant de garder en mémoire le fait que vous acceptez les cookies afin de ne plus vous importuner lors de votre prochaine visite</li></ul>
<!-- /wp:list -->

<!-- wp:heading -->
<h2>Vos droits concernant les données personnelles</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Vous avez le droit de consultation, demande de modification ou d’effacement sur l’ensemble de vos données personnelles. Vous pouvez également retirer votre consentement au traitement de vos données.</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2>Contact délégué à la protection des données</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Hugo Clarke-Wing, Président du Bureau — <a href="mailto:hugo.clarke-wing@agepac.org">hugo.clarke-wing@agepac.org</a></p>
<!-- /wp:paragraph -->
EOD,
            'restricted' => false,
        ]);
    }
}
