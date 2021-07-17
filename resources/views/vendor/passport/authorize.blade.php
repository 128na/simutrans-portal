@extends('layouts.front')

@section('title', '認証確認')

@section('content')
    <div class="container mt-5">
        <h2>認証確認</h2>
        <p>なんと <strong>{{ $client->name }}</strong>が 認証してほしそうにこちらをみている！</p>

        <!-- Scope List -->
        @if (count($scopes) > 0)
            <div class="scopes">
                <p><strong>許可する権限:</strong></p>

                <ul>
                    @foreach ($scopes as $scope)
                        <li>{{ $scope->description }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="form-group">
            <button type="submit" class="btn btn-primary" form="approve">認証する</button>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-secondary" form="deny">やめる</button>
        </div>

        <form method="post" id="approve" action="{{ route('passport.authorizations.approve') }}">
            @csrf
            <input type="hidden" name="state" value="{{ $request->state }}">
            <input type="hidden" name="client_id" value="{{ $client->id }}">
            <input type="hidden" name="auth_token" value="{{ $authToken }}">

        </form>
        <form method="post" id="deny" action="{{ route('passport.authorizations.deny') }}">
            @csrf
            @method('DELETE')
            <input type="hidden" name="state" value="{{ $request->state }}">
            <input type="hidden" name="client_id" value="{{ $client->id }}">
            <input type="hidden" name="auth_token" value="{{ $authToken }}">
        </form>
    </div>
