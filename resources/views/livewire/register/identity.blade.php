<div role="tabpanel"
     class="tab-pane fade {{ $active ? 'active show' : null }}">

    @if($invitationNotFound)
        @include('livewire.register.invitation-not-found')
    @elseif(! is_null($invitation))
        @include('livewire.register.identity.invitation-confirmation')
    @elseif($shouldSearchByName)
        @include('livewire.register.identity.name-form')
    @else
        @include('livewire.register.identity.full-form')
    @endif
</div>
