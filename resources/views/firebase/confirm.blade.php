@extends('layouts.front')

@section('title', '連携確認')

@section('content')
    <div class="container mt-5">
        <h2>連携確認</h2>
        <p><strong>{{ config("firebase.projects.$provider.name") }}</strong>でログインできるようにしますか？</p>
        <form method="POST" action="{{ route('firebase.accept', $provider) }}">
            @csrf
            <div class="form-group">
                <button type="submit" class="btn btn-primary">連携する</button>
            </div>
            <div class="form-group">
                <button class="btn btn-secondary" onclick="window.close()">やめる</button>
            </div>
        </form>
    </div>
