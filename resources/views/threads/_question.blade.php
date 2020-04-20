{{-- Editing the question --}}
<div class="card mb-4" v-if="editing">
    <div class="card-header d-flex align-items-center">
        <input type="text" class="form-control" v-model="form.title">
    </div>
    <div class="card-body">
        <div class="form-group">
            <wysiwyg v-model="form.body"></wysiwyg>
        </div>
    </div>
    <div class="card-footer d-flex">
        <button class="btn btn-sm btn-primary mr-2" @click="update">Sauvegarder</button>
        <button class="btn btn-sm btn-secondary" @click="resetForm">Annuler</button>

        @can('update', $thread)
            <form method="POST" action="{{ $thread->path() }}" class="ml-auto">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-sm btn-danger">Supprimer</button>
            </form>
        @endcan
    </div>
</div>

{{-- Viewing the question --}}
<div class="card mb-4" v-else>
    <div class="card-header d-flex align-items-center">
        <img src="{{  $thread->creator->avatar_path }}" alt="{{ $thread->creator->name }}" width="25" height="25" class="mr-2">
        <div class="flex-grow-1">
            <a href="{{ route('profile', $thread->creator) }}">{{ $thread->creator->name }}</a> posted: <span v-text="title"></span>
        </div>
    </div>
    <div class="card-body" v-html="body"></div>
    <div class="card-footer" v-if="authorize('owns', thread)">
        <button class="btn btn-sm btn-outline-secondary" @click="editing = true">Modifier</button>
    </div>
</div>

@push('styles')
    <link href="{{ asset('css/vendor/trix.css') }}" rel="stylesheet">
@endpush
