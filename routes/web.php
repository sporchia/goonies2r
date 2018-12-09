<?php

use Illuminate\Http\Request;

Route::post('randomize', 'RandomizerController@randomize')->middleware('throttle:20,60');

Route::post('hash', 'GameController@getHash')->middleware('throttle:60,1');

// @TODO: perhaps a front end page that checks their localStorage for prefered locale?
Route::get('h/{hash}', function(Request $request, $hash) {
    return redirect(config('app.locale') . '/h/' . $hash);
});

Route::prefix('{lang?}')->middleware('locale')->group(function () {
    Route::view('/', 'welcome');

    Route::view('start', 'start');

    Route::view('randomizer', 'randomizer');

    Route::view('resources', 'resources');

    Route::view('options', 'options');

    Route::view('updates', 'updates');

    Route::get('h/{hash}', 'GameController@fromHash');
});

// Catchall for bad requests
Route::any('{path?}', function(Request $request, string $path = '') {
	return response([
        'message' => 'Invalid Endpoint.',
        'verb' => $request->method(),
        'uri' => $path,
    ], 405);
})->where('path', '(.*)?');
