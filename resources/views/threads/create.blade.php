@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header">
                    Créer une nouvelle discussion
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('threads') }}">
                        @csrf
                        <div class="form-group">
                            <label for="channel_id">Chaîne</label>
                            <select class="form-control{{ $errors->has('channel_id') ? ' is-invalid' : '' }}" id="channel_id" name="channel_id" required>
                                <option value="" disabled{{ is_null(old('channel_id')) ? ' selected' : '' }}></option>
                                @foreach($channels as $channel)
                                    <option value="{{ $channel->id }}"{{ old('channel_id') == $channel->id ? ' selected' : '' }}>
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
                            <wysiwyg name="body"></wysiwyg>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Publier</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
