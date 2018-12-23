<?php

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;

class GraphConnectionsCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testWithNoOptions()
    {
        $this->artisan('graph:connections', [
            '--output' => 'none',
        ])->assertExitCode(0);
    }

    /**
     * @return void
     */
    public function testWithItems()
    {
        $this->artisan('graph:connections', [
            '--item' => 'Hammer',
            '--output' => 'none',
        ])->assertExitCode(0);
    }

    /**
     * @return void
     */
    public function testCanChangeStartingLocation()
    {
        $this->artisan('graph:connections', [
            'from' => 'Room 02',
            '--output' => 'none',
        ])->assertExitCode(0);
    }

    /**
     * @return void
     */
    public function testWithRandomize()
    {
        $this->artisan('graph:connections', [
            '--randomize' => true,
            '--output' => 'none',
        ])->assertExitCode(0);
    }
}
