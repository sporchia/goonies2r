<?php

namespace Tests\Unit\Support;

use App\Support\Flips;
use Tests\TestCase;

class FlipsTest extends TestCase
{
    /**
     * @return void
     */
    public function testCreateBps()
    {
        $flips = new Flips;

        $data = $flips->createBpsFromFiles('tests/Unit/samples/empty.bin', 'tests/Unit/samples/bytes.bin');

        $this->assertEquals(file_get_contents('tests/Unit/samples/patch.bps'), $data);
    }

    /**
     * @return void
     */
    public function testUnreadableFilesThrowException()
    {
        $this->expectException(\Exception::class);

        $flips = new Flips;

        $flips->createBpsFromFiles('tests/Unit/samples/thisfilereallydoesntexist', 'tests/Unit/samples/bytes.bin');
    }

    /**
     * @return void
     */
    public function testApplyBps()
    {
        $flips = new Flips;

        $data = $flips->applyBpsToFile('tests/Unit/samples/empty.bin', 'tests/Unit/samples/patch.bps');

        $this->assertEquals(file_get_contents('tests/Unit/samples/bytes.bin'), $data);
    }

    /**
     * @return void
     */
    public function testUnreadableFilesThrowExceptionInApply()
    {
        $this->expectException(\Exception::class);

        $flips = new Flips;

        $flips->applyBpsToFile('tests/Unit/samples/thisfilereallydoesntexist', 'tests/Unit/samples/patch.bps');
    }
}
