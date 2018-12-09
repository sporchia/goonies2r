@extends('layouts.default', ['title' => __('navigation.resources') . ' - '])

@section('content')
<h1 class="text-white">{{ __('resources.header') }}</h1>
<div class="card card-body bg-dark text-white">
    <div class="card border-info mt-4">
        <div class="card-header bg-info">
            <h3 class="card-title text-white">{{ __('resources.cards.external.header') }}</h3>
        </div>
        <div class="card-body bg-dark">
            @foreach (__('resources.cards.external.content') as $block)
                <p>{!! $block !!}</p>
            @endforeach
        </div>
    </div>

    <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-5161309967767506" data-ad-slot="9849787408" data-ad-format="auto"></ins>

    <div class="card border-info mt-4">
        <div class="card-header bg-info">
            <h3 class="card-title text-white">{{ __('resources.cards.changes.header') }}</h3>
        </div>
        <div class="card-body bg-dark">
            @foreach (__('resources.cards.changes.sections') as $section)
            <h4>{{ $section['header'] }}</h4>
                @foreach ($section['content'] as $block)
                    <p>{!! $block !!}</p>
                @endforeach
            @endforeach
        </div>
    </div>
</div>
@overwrite
