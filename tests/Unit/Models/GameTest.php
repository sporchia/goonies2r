<?php

namespace Tests\Unit\Models;

use App\Models\Game;
use Tests\TestCase;

class GameTest extends TestCase
{
    /**
     * @return void
     */
    public function testEmptySpoiler()
    {
        $game = new Game;

        $this->assertEmpty($game->spoiler);
    }

    /**
     * @return void
     */
    public function testSpoilerSet()
    {
        $game = new Game;

        $spoiler = [
            'test' => 1,
        ];

        $game->spoiler = $spoiler;

        $this->assertEquals($spoiler, $game->spoiler);
    }
}
