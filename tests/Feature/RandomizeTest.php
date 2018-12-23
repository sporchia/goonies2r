<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Game;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RadndomizeTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function testRandomizeEndpointNoOptions()
    {
        $response = $this->post('/randomize');

        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function testGameDoesntExist()
    {
        $response = $this->get('/en/h/thissurelydoesntexist');

        $response->assertStatus(404);
    }

    /**
     * @return void
     */
    public function testGameSaves()
    {
        $this->post('/randomize');

        $game = Game::latest()->first();

        $response = $this->get('/en/h/' . $game->hash);

        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function testPatchGameNoHash()
    {
        $response = $this->post('/hash');

        $response->assertStatus(422);
    }

    /**
     * @return void
     */
    public function testPatchGameDoesntExist()
    {
        $response = $this->post('/hash', [
            'hash' => 'thissurelydoesntexist',
        ]);

        $response->assertStatus(404);
    }

    /**
     * @return void
     */
    public function testPatchOnGameSave()
    {
        $this->post('/randomize');

        $game = Game::latest()->first();

        $response = $this->post('/hash', [
            'hash' => $game->hash,
        ]);

        $response->assertStatus(200);
        $this->assertEquals($game->bps, $response->getContent());
    }
}
