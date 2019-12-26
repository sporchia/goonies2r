@extends('layouts.default')

@section('content')
<div class="text-center">
    <picture>
        <source srcset="/img/logo_113.png" media="(max-width: 226px)">
        <source srcset="/img/logo_226.png" media="(max-width: 452px)">
        <source srcset="/img/logo_452.png" media="(max-width: 904px)">
        <img src="/img/logo_904.png" alt="Goonies 2: Randomizer VT">
    </picture>
    <div class="btn-wrapper">
        <div class="btn-cta mt-2">
            <a class="btn btn-primary btn-lg" href="/{{ app()->getLocale() }}/start" role="button">{{ __('navigation.start_playing') }}</a>
        </div>
        <div class="card card-body bg-dark text-left text-white lead mt-2">
            @foreach (__('welcome.content') as $block)
                <p>{!! $block !!}</p>
            @endforeach
        </div>
    </div>
    <ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-5161309967767506" data-ad-slot="9849787408" data-ad-format="auto"></ins>
</div>
@overwrite
