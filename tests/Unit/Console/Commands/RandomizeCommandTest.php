<?php

namespace Tests\Unit\Console\Commands;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RandomizeommandTest extends TestCase
{
    /**
     * @return void
     */
    public function testWithNoOptionsNoFile()
    {
        $this->artisan('randomize', [
        ])->assertExitCode(100);
    }

    /**
     * @return void
     */
    public function testBadOutputDirectory()
    {
        $this->artisan('randomize', [
            'input_file' => base_path('tests/Unit/samples/garbage.nes'),
            'output_directory' => '/thisdirectoryshouldntexist'
        ])->assertExitCode(101);
    }

    /**
     * @return void
     */
    public function testNoRomOption()
    {
        $this->artisan('randomize', [
            'input_file' => base_path('tests/Unit/samples/garbage.nes'),
            'output_directory' => sys_get_temp_dir(),
            '--no-rom' => true,
        ])->assertExitCode(0);
    }
}
