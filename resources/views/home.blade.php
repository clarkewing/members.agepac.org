@extends('layouts.app')

@push('styles')
<style type="text/css">
    #overflowContainer::before {
        content: '';
        width: 2rem;
        height: calc(100% + 2px);
        position: absolute;
        right: 0;
        top: -1px;
        background: linear-gradient(to left, rgba(255,255,255,1) 0%, rgba(255,255,255,0.5) 45%, rgba(255,255,255,0) 100%);
        z-index: 1;
        pointer-events: none;
    }
</style>
@endpush

@section('content')
<div class="container bg-white shadow-sm" style="margin-top: -1.5rem;">
    <div class="row justify-content-center py-4">
        <div class="col-md-12">
            <h3>Quoi de neuf ?</h3>
            <div id="overflowContainer" class="position-relative mb-3">
                <div class="d-flex flex-row flex-nowrap mx-0" style="overflow-x: scroll; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch;">
                    <div class="card d-inline-flex bg-dark text-white ml-0 mr-3" style="width: 300px; scroll-snap-align: start; flex: 0 0 auto;">
                        <div class="card-body pb-0">
                            <h5 class="card-title text-truncate">Inscription au RCS pour auto-entrepreneurs</h5>
                        </div>
                        <div class="card-body pt-0">
                            <p class="card-text small">Ouvert par Hugo Clarke-Wing il y a 4 minutes</p>
                        </div>
                    </div>
                    <div class="card d-inline-flex bg-dark text-white ml-0 mr-3" style="width: 300px; scroll-snap-align: start; flex: 0 0 auto;">
                        <div class="card-body pb-0">
                            <h5 class="card-title text-truncate">Nouvelle formation Advanded UPRT</h5>
                        </div>
                        <div class="card-body pt-0">
                            <p class="card-text small">Théo Chamiot-Prieur a répondu hier</p>
                        </div>
                    </div>
                    <div class="card d-inline-flex bg-dark text-white ml-0 mr-3" style="width: 300px; scroll-snap-align: start; flex: 0 0 auto;">
                        <div class="card-body pb-0">
                            <h5 class="card-title text-truncate">Séminaire Intégration EPL 2019</h5>
                        </div>
                        <div class="card-body pt-0">
                            <p class="card-text small">Pierre-Henri Lebrun a répondu le 20 septembre</p>
                        </div>
                    </div>
                    <div class="card d-inline-flex bg-dark text-white ml-0 mr-3" style="width: 300px; scroll-snap-align: start; flex: 0 0 auto;">
                        <div class="card-body pb-0">
                            <h5 class="card-title text-truncate">TB 20 Montpellier</h5>
                        </div>
                        <div class="card-body pt-0">
                            <p class="card-text small">Julian Krumel a répondu le 19 septembre</p>
                        </div>
                    </div>
                    <div class="card d-inline-flex bg-dark text-white ml-0 mr-3" style="width: 300px; scroll-snap-align: start; flex: 0 0 auto;">
                        <div class="card-body pb-0">
                            <h5 class="card-title text-truncate">Sélection GAF LCE 2.0</h5>
                        </div>
                        <div class="card-body pt-0">
                            <p class="card-text small">Lénaïc Guillaud a répondu le 19 septembre</p>
                        </div>
                    </div>
                    <div class="card d-inline-flex bg-dark text-white ml-0 mr-3" style="width: 300px; scroll-snap-align: start; flex: 0 0 auto;">
                        <div class="card-body pb-0">
                            <h5 class="card-title text-truncate">Intérêt de postuler via la LCE</h5>
                        </div>
                        <div class="card-body pt-0">
                            <p class="card-text small">Guillaume Olivier a répondu le 10 septembre</p>
                        </div>
                    </div>
                    <div class="card d-inline-flex bg-dark text-white ml-0 mr-3" style="width: 300px; scroll-snap-align: start; flex: 0 0 auto;">
                        <div class="card-body pb-0">
                            <h5 class="card-title text-truncate">Fcl.610</h5>
                        </div>
                        <div class="card-body pt-0">
                            <p class="card-text small">Jean-Manuel Chastagner a répondu le 9 septembre</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <h3>À la une</h3>
            <section>
                <h4 class="text-primary"><a href="showthread.php?2830">Compte rendu Assemblée Générale 2019</a></h4>
                <p>Bonjour à toutes et à tous,<br>
                    <br>
                    <br>
                    C'est avec grand plaisir (et retard) que je vous soumets le compte rendu de l'Assemblée Générale 2019 ci-dessous.<br>
                    <br>
                    Durant à l'AG ont été élus au Bureau de l'Association :<br>
                    <strong>Président :</strong> Simon Louyot, EPL/S13<br>
                    <strong>Vice-président :</strong> Pierre Guillot, ATPL12<br>
                    <strong>Trésorier :</strong> Florian Besson, ATPL14<br>
                    <strong>Trésorier adjoint :</strong> Timothée Barry EPL/S14<br>
                    <strong>Secrétaire général :</strong> Hugo Clarke-Wing, EPL/S15<br>
                    <strong>Secrétaires adjoints :</strong> Thomas Botzong, EPL/S14 et Sofian Lehoucq, ATPL17<br>
                    <strong>Responsable communication :</strong> Victoria Durban, EPL/S13<br>
                    <strong>Responsables événements :</strong> Fabien Brunelet, ATPL15 ; Jeff Mhanna, EPL/S17 et Arthur Picard, EPL/S18<br>
                    <br>
                    <br>
                    Au Conseil d'Administration (CA) :<br>
                    <strong>RP 2008 :</strong> Steven Otal<br>
                    <strong>RP 2009 :</strong> Paul Rameau<br>
                    <strong>RP 2010 :</strong> César Chroscik<br>
                    <strong>RP 2011 :</strong> François Tissot<br>
                    <strong>RP 2012 :</strong> Jacques Preiss<br>
                    <strong>RP 2013 :</strong> Vincent Saget<br>
                    <strong>RP 2014 :</strong> Timothée Barry<br>
                    <strong>RP 2015 :</strong> Théo Chamiot-Prieur<br>
                    <strong>RP 2016 :</strong> Thomas Izarn<br>
                    <strong>RP 2017 :</strong> Fabien Brunelet<br>
                    <strong>RP 2018 :</strong> Lilan Berquier<br>
                    <strong>RP U&amp;P :</strong> David Robert<br>
                    <strong>RP CATPL :</strong> Jean-Manuel Chastagner</p>
                </section>
        </div>
        <div class="col-md-4">
            <h3>Activité</h3>
            <table class="table">
                <tbody>
                    <tr><td>
                        <p class="mb-0">
                            <a href="member.php?3396"><span style="text-transform:capitalize;">Baptiste Le Neouanic</span></a> vient de modifier le total de ses heures de vol
                        </p>
                        <p class="small text-muted text-right mb-0">17.09</p>
                    </td></tr>
                    <tr><td>
                        <p class="mb-0">
                            La fiche <a href="airline.view.php?do=redirect&amp;titre=XLR Parachutisme">XLR Parachutisme</a> a été mise en ligne
                        </p>
                        <p class="small text-muted text-right mb-0">10.09</p>
                    </td></tr>
                    <tr><td>
                        <p class="mb-0">
                            <a href="member.php?3382"><span style="text-transform:capitalize;">Julien Bloyet</span></a> a rejoint <a href="airline.view.php?do=redirect&amp;titre=Big Air Parachutisme">Big Air Parachutisme</a> - Pilote Largueur
                        </p>
                        <p class="small text-muted text-right mb-0">10.09</p>
                    </td></tr>
                    <tr><td>
                        <p class="mb-0">
                            La fiche <a href="airline.view.php?do=redirect&amp;titre=Airways Formation">Airways Formation</a> a été modifiée
                        </p>
                        <p class="small text-muted text-right mb-0">10.09</p>
                    </td></tr>
                    <tr><td>
                        <p class="mb-0">
                            <a href="member.php?3419"><span style="text-transform:capitalize;">Olivier Flin</span></a> vient de modifier le total de ses heures de vol
                        </p>
                        <p class="small text-muted text-right mb-0">10.09</p>
                    </td></tr>
                    <tr><td>
                        <p class="mb-0">
                            <a href="member.php?3382"><span style="text-transform:capitalize;">Julien Bloyet</span></a> vient de mettre à jour son profil
                        </p>
                        <p class="small text-muted text-right mb-0">10.09</p>
                    </td></tr>
                    <tr><td>
                        <p class="mb-0">
                            <a href="member.php?3438"><span style="text-transform:capitalize;">Jean-Manuel Chastagner</span></a> vient de mettre à jour son profil
                        </p>
                        <p class="small text-muted text-right mb-0">07.09</p>
                    </td></tr>
                    <tr><td>
                        <p class="mb-0">
                            <a href="member.php?2895"><span style="text-transform:capitalize;">Victorien Prunier</span></a> vient de mettre à jour son profil
                        </p>
                        <p class="small text-muted text-right mb-0">06.09</p>
                    </td></tr>
                    <tr><td>
                        <p class="mb-0">
                            <a href="member.php?2895"><span style="text-transform:capitalize;">Victorien Prunier</span></a> suit la formation Air Formation - Formateur CRM
                        </p>
                        <p class="small text-muted text-right mb-0">06.09</p>
                    </td></tr>
                    <tr><td>
                        <p class="mb-0">
                            <a href="member.php?118"><span style="text-transform:capitalize;">Marine Arnaud-battandier</span></a> a rejoint <a href="airline.view.php?do=redirect&amp;titre=HOP!">HOP!</a> - OPL EJET
                        </p>
                        <p class="small text-muted text-right mb-0">02.09</p>
                    </td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
