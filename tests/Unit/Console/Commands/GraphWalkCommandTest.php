<?php

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;

class GraphWalkCommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testWithNoOptions()
    {
        $this->artisan('graph:walk', [
            '--output' => 'none',
        ])->assertExitCode(0);
    }

    /**
     * @return void
     */
    public function testWithItems()
    {
        $this->artisan('graph:walk', [
            '--item' => ['Hammer', 'Ladder', 'KeysC0', 'DivingSuit', 'Candle'],
            '--output' => 'none',
        ])->assertExitCode(0);
    }

    /**
     * @return void
     */
    public function testWithItemsUnreachable()
    {
        $this->artisan('graph:walk', [
            'to' => 'Nowhere',
            '--item' => ['Hammer'],
            '--output' => 'none',
        ])->assertExitCode(100);
    }

    /**
     * @return void
     */
    public function testCanChangeStartingLocation()
    {
        $this->artisan('graph:walk', [
            'from' => 'Room 02',
            '--item' => ['Hammer', 'Ladder', 'KeysC0', 'DivingSuit', 'Candle'],
            '--output' => 'none',
        ])->assertExitCode(0);
    }

    /**
     * @return void
     */
    public function testEndUnreachable()
    {
        $this->artisan('graph:walk', [
            'from' => 'Room 26',
            'to' => 'Room 27',
            '--item' => ['KeysC0'],
            '--output' => 'none',
        ])->assertExitCode(101);
    }
}
