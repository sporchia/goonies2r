<?php

namespace App\Http\Controllers;

use App\Exceptions\NoLocationsAvailableException;
use App\Http\Requests\CreateRandomizedGame;
use App\Models\Game;
use App\Rom;
use App\Services\RandomizerService;
use App\Support\Flips;
use App\Support\ItemCollection;
use App\World;
use Illuminate\Http\Response;

/**
 * Handle requests for randomization from api.
 */
class RandomizerController extends Controller
{
    /**
     * Generate a randomized game.
     *
     * @param \App\Http\Requests\CreateRandomizedGame  $request  the request object
     *
     * @return \Illuminate\Http\Response
     */
    public function randomize(CreateRandomizedGame $request): Response
    {
        $flips = new Flips;

        $tmp_file = tempnam(sys_get_temp_dir(), 'RandomizerController-');

        if ($tmp_file === false) {
            // @codeCoverageIgnoreStart
            return response('Unable to create temp file', 500);
            // @codeCoverageIgnoreEnd
        }

        file_put_contents($tmp_file, $flips->applyBpsToFile(env('ROM_BASE'), env('PATCH_BASE', public_path('bps/base.bps'))));

        $rom = new Rom($tmp_file);

        unlink($tmp_file);

        $rand = new RandomizerService;
        $generated = false;
        do {
            $world = new World(new ItemCollection, $request->validated());
            try {
                $rand->randomize($world);

                $generated = true;
                // @codeCoverageIgnoreStart
            } catch (NoLocationsAvailableException $e) {
                // @codeCoverageIgnoreEnd
                // only catch this, eventually we will generate an okay game.
            }
        } while ($generated === false);

        $rand->writeToRom($world, $rom);

        $tmp_file = tempnam(sys_get_temp_dir(), 'RandomizerController-Bps-');

        if ($tmp_file === false) {
            // @codeCoverageIgnoreStart
            return response('Unable to create temp file', 500);
            // @codeCoverageIgnoreEnd
        }

        $rom->save($tmp_file);

        $spoiler = $rand->getSpoiler($world);
        $game = Game::create([
            'spoiler' => $spoiler,
        ]);

        $data = $flips->createBpsFromFiles(env('ROM_BASE'), $tmp_file, [
            'created' => now()->toIso8601String(),
            'version' => RandomizerService::VERSION,
            'hash'    => $game->hash,
            'meta'    => $spoiler['meta'],
        ]);

        unlink($tmp_file);

        $game->bps = $data;
        $game->save();

        return response($data, 200, [
            'Content-Type' => 'application/octet-stream',
        ]);
    }
}
