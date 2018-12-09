@extends('layouts.default')

@section('content')
<div class="card border-info text-white bg-dark">
	<div class="card-header bg-info">
		<h3 class="card-title">Ut oh...</h3>
	</div>
	<div class="card-body">
		<h4>{{ $exception->getMessage() }}</h4>
	</div>
</div>

<ins class="adsbygoogle" style="display:block" data-ad-client="ca-pub-5161309967767506" data-ad-slot="9849787408" data-ad-format="auto"></ins>
@overwrite
