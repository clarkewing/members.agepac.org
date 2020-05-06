@extends('threads._layout')

@section('main')
    <h2 class="mb-3">Ouvrir une discussion</h2>

    <form method="POST" action="{{ route('threads.store') }}">
        @csrf

        <div class="form-group">
            <label for="channel_id">Catégorie</label>
            <select class="form-control{{ $errors->has('channel_id') ? ' is-invalid' : '' }}" id="channel_id" name="channel_id" required>
                <option value="" disabled{{ is_null(old('channel_id')) ? ' selected' : '' }}></option>
                @foreach($channels as $channel)
                    <option value="{{ $channel->id }}"{{ old('channel_id', Request::query('channel_id')) == $channel->id ? ' selected' : '' }}>
                        {{ $channel->name }}
                    </option>
                @endforeach
            </select>

            @error('channel_id')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="title">Titre</label>
            <input type="text" class="form-control{{ $errors->has('title') ? ' is-invalid' : '' }}" id="title" name="title"
                   value="{{ old('title') }}" required>

            @error('title')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-group">
            <label for="body">Corps</label>
            <wysiwyg name="body" :error="{{ json_encode($errors->first('body')) }}"></wysiwyg>
        </div>

        <div class="form-group d-flex">
            <button type="submit" class="btn btn-success ml-auto">Publier</button>
        </div>
    </form>
@endsection
