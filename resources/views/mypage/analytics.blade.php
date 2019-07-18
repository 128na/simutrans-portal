@extends('layouts.mypage')

@section('title', __('Access Analytics'))

@section('content')
    <script>
        window.articles = @json($articles);
        window.lang = {
            'Types' :  "@lang('Types')",
            'Aggregation method' :  "@lang('Aggregation method')",
            'Term' :  "@lang('Term')",
            'Target' :  "@lang('Target')",
            'Articles' :  "@lang('Articles')",
            'Daily': "@lang('Daily')",
            'Mothly': "@lang('Mothly')",
            'Yearly': "@lang('Yearly')",
            'Transition': "@lang('Transition')",
            'Total': "@lang('Total')",
            'Page Views': "@lang('Page Views')",
            'Conversions': "@lang('Conversions')",
            'Toggle All': "@lang('Toggle All')",
        };
    </script>
    <div class="mypage">
        <div id="app-analytics"></div>
    </div>
@endsection
