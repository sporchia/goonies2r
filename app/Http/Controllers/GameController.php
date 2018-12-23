<?php

namespace App\Http\Controllers;

use App\Models\Game;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * Handle requests for already generated games.
 */
class GameController extends Controller
{
    /**
     * return the view to load the already generated game.
     *
     * @param \Illuminate\Http\Request $request the request object
     * @param string                  $lang    the requested language
     * @param string                  $hash    identifier to the already generated game
     *
     * @return \Illuminate\Http\Response
     */
    public function fromHash(Request $request, string $lang, string $hash) : Response
    {
        $game = Game::where('hash', $hash)->first();

        if (!$game) {
            abort(404, 'Game not found');
        }

        return response()->view('from_hash', ['hash' => $hash]);
    }

   /**
     * return the BPS for an already generated game.
     *
     * @param \Illuminate\Http\Request $request the request object
     *
     * @return \Illuminate\Http\Response
     */
    public function getHash(Request $request) : Response
    {
        if (!$request->has('hash')) {
            return response('Hash not passed', 422);
        }

        $game = Game::where('hash', $request->input('hash'))->first();

        if (!$game) {
            return response('Game not found', 404);
        }

        return response($game->bps, 200, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }
}
