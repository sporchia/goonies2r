@extends('layouts.default', ['title' => __('navigation.options') . ' - '])

@section('content')
<h1 class="text-white">{{ __('options.header') }}</h1>
<div  id="options" class="card card-body bg-dark text-white">
    <h2>{!! __('options.subheader') !!}</h2>

    <div class="card border-info mt-4">
        <div class="card-header bg-info">
            <h3 class="card-title text-white">{{ __('options.cards.coming.header') }}</h3>
        </div>
        <div class="card-body bg-dark">
            @foreach (__('options.cards.coming.content') as $block)
                <p>{!! $block !!}</p>
            @endforeach
        </div>
    </div>

	<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-5161309967767506" data-ad-slot="9849787408" data-ad-format="auto"></ins>

	<div class="card border-info mt-4" id="item-pool">
		<div class="card-header bg-info">
			<h3 class="card-title text-white">{{ __('options.cards.item_pool') }}</h3>
		</div>
		<div class="card-body bg-dark">
			<div class="row">
				<div class="col-md-6">
					<ul>
                        <li>1x Yo-Yo</li>
                        <li>1x Slingshot</li>
                        <li>1x Boomerang</li>
                        <li>1x Ladder</li>
                        <li>1x Hammer</li>
                        <li>1x Candle</li>
                        <li>1x Diving Suit</li>
                        <li>1x Transceiver</li>
                        <li>1x Glasses</li>
                        <li>1x Helmet</li>
                        <li>1x Raincoat</li>
                    </ul>
				</div>
				<div class="col-md-6">
					<ul>
                        <li>1x Hyper Shoes</li>
                        <li>1x Jump Shoes</li>
                        <li>1x Bulletproof Vest</li>
                        <li>4x Key Rings</li>
                        <li>4x Bomb Boxes</li>
                        <li>4x Fire Boxes</li>
                        <li>6x Magic Locator Devices</li>
                        <li>10x Hints in safes</li>
                    </ul><ul>
                        <li>6x Goonies</li>
                        <li>1x Mermaid</li>
					</ul>
				</div>
			</div>
		</div>
    </div>

</div>
@overwrite
