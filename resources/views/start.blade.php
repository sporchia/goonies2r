@extends('layouts.default', ['title' => __('navigation.start_playing') . ' - '])

@section('content')
<h1 class="text-white">{{ __('start.header') }}</h1>
<div class="card card-body bg-dark">
    <h2 class="text-white">{{ __('start.subheader') }}</h2>

    <div class="card border-info mt-4">
        <div class="card-header bg-info">
            <h3 class="card-title text-white">{{ __('start.cards.rom.header') }}</h3>
        </div>
        <div class="card-body bg-dark text-white">
            @foreach (__('start.cards.rom.content') as $block)
                <p>{!! $block !!}</p>
            @endforeach
        </div>
    </div>

    <div class="card border-info mt-4">
        <div class="card-header bg-info">
            <h3 class="card-title text-white">{{ __('start.cards.randomize.header') }}</h3>
        </div>
        <div class="card-body bg-dark text-white">
            @foreach (__('start.cards.randomize.content') as $block)
                <p>{!! $block !!}</p>
            @endforeach
        </div>
    </div>

    <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-5161309967767506" data-ad-slot="9849787408" data-ad-format="auto"></ins>

    <div class="card border-info mt-4">
        <div class="card-header bg-info">
            <h3 class="card-title text-white">{{ __('start.cards.emulator.header') }}</h3>
        </div>
        <div class="card-body bg-dark text-white">
            @foreach (__('start.cards.emulator.content') as $block)
                <p>{!! $block !!}</p>
            @endforeach
        </div>
    </div>

    <div class="card border-info mt-4">
        <div class="card-header bg-info">
            <h3 class="card-title text-white">{{ __('start.cards.play.header') }}</h3>
        </div>
        <div class="card-body bg-dark text-white">
            @foreach (__('start.cards.play.content') as $block)
                <p>{!! $block !!}</p>
            @endforeach
        </div>
    </div>

</div>
@overwrite
