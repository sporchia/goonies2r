@extends('layouts.base')

@section('window')
<div id="app">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
        <a class="navbar-brand" href="{{ app()->isLocale('en') ? '' : '/' . app()->getLocale() }}/">
            <img src="/img/logo_113.png" title="Goonies 2 Randomizer" alt="Goonies 2 Randomizer logo" />
        </a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbar" aria-controls="navbar" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div id="navbar" class="navbar-collapse collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item{!! (request()->path() == app()->getLocale()  . '/start') ? ' active' : '' !!}">
                    <a class="nav-link" href="{{ app()->isLocale('en') ? '' : '/' . app()->getLocale() }}/start">{{ __('navigation.start_playing') }}</a>
                </li>
                <li class="nav-item{!! (request()->path() == app()->getLocale()  . '/randomizer') ? ' active' : '' !!}">
                    <a class="nav-link" href="{{ app()->isLocale('en') ? '' : '/' . app()->getLocale() }}/randomizer">{{ __('navigation.randomizer') }}</a>
                </li>
            </ul>
            <ul class="navbar-nav ml-auto">
                <streams></streams>
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link dropdown-toggle{!! (in_array(substr(request()->path(), 3), ['resources', 'options', 'updates'])) ? ' active' : '' !!}" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">{{ __('navigation.help') }} <span class="caret"></span></a>
                    <div class="dropdown-menu dropdown-menu-right">
                        <a class="dropdown-item{!! (request()->path() == app()->getLocale()  . '/resources') ? ' active' : '' !!}" href="{{ app()->isLocale('en') ? '' : '/' . app()->getLocale() }}/resources">{{ __('navigation.resources') }}</a>
                        <a class="dropdown-item{!! (request()->path() == app()->getLocale()  . '/options') ? ' active' : '' !!}" href="{{ app()->isLocale('en') ? '' : '/' . app()->getLocale() }}/options">{{ __('navigation.options') }}</a>
                        <a class="dropdown-item{!! (request()->path() == app()->getLocale()  . '/updates') ? ' active' : '' !!}" href="{{ app()->isLocale('en') ? '' : '/' . app()->getLocale() }}/updates">{{ __('navigation.updates') }}</a>
                        <a class="dropdown-item" href="https://github.com/sporchia/goonies2r/issues/new" target="_blank" rel="noopener noreferrer">{{ __('navigation.report_issue') }}</a>
                    </div>
                </li>
            </ul>
        </div>
    </nav>
    <div class="clearfix" style="padding-top:90px"></div>
    <div class="container">
    @yield('content')
    </div>
</div>
@overwrite
