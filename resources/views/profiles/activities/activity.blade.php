<div class="card mb-4 shadow">
    <div class="card-header bg-white">
        {{ $heading }}
    </div>
    @if(isset($body))
        <div class="card-body">
            {{ $body }}
        </div>
    @endif
</div>
