@extends('layouts.default', ['title' => __('navigation.updates') . ' - '])

@section('content')
<h1 class="text-white">{{ __('updates.header') }}</h1>
<div class="card card-body bg-dark">
    <div class="card border-info mt-4">
        <div class="card-header bg-info">
            <h3 class="card-title text-white">{{ __('updates.cards.v1.header') }}</h3>
        </div>
        <div class="card-body bg-dark text-white">
            @foreach (__('updates.cards.v1.content') as $block)
                <p>{!! $block !!}</p>
            @endforeach
        </div>
    </div>
</div>
@overwrite
