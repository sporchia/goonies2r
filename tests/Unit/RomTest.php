<?php

namespace Tests\Unit;

use App\Rom;
use Tests\TestCase;

class RomTest extends TestCase
{
    /**
     * @return void
     */
    public function testReadFile(): void
    {
        $rom = new Rom(base_path('tests/Unit/samples/bytes.bin'));

        $this->assertEquals([0x03, 0x04, 0x05], $rom->read(0x03, 0x03));
    }

    /**
     * @return void
     */
    public function testLoadFileThrowsOnBadFile(): void
    {
        $this->expectException(\Exception::class);

        $rom = new Rom(base_path('tests/Unit/samples/thisfilecertainlydoesntexist.bin'));
    }

    /**
     * @covers App\Rom::setTextSpeed
     *
     * @dataProvider textSpeeds
     *
     * @param string  $speed  speed key
     * @param int  $byte  frame delay
     * @param int  $sfx  sound effect to plat
     *
     * @return void
     */
    public function testSetTextSpeed(string $speed, int $byte, int $sfx): void
    {
        $rom = new Rom;

        $rom->setTextSpeed($speed);

        $this->assertEquals([[$byte], [$sfx]], [$rom->read(0x4027), $rom->read(0x406d)]);
    }

    /**
     * Speed text provider.
     *
     * @return array
     */
    public function textSpeeds(): array
    {
        return [
            ['instant', 0x00, 0x00],
            ['fast', 0x01, 0x05],
            ['default', 0x05, 0x03],
        ];
    }

    /**
     * @covers App\Rom::getWriteLog
     *
     * @return void
     */
    public function testWriteLogEmptyBeforeWrites(): void
    {
        $rom = new Rom;

        $this->assertEquals([], $rom->getWriteLog());
    }

    /**
     * @covers App\Rom::getWriteLog
     *
     * @return void
     */
    public function testWriteLog(): void
    {
        $rom = new Rom;

        $rom->write(0x00, 'hello');

        $this->assertEquals([[0 => [0x68, 0x65, 0x6c, 0x6c, 0x6f]]], $rom->getWriteLog());
    }

    /**
     * @covers App\Rom::save
     *
     * @return void
     */
    public function testSaveFile(): void
    {
        $tmp_file = tempnam(sys_get_temp_dir(), __CLASS__);
        $rom = new Rom(base_path('tests/Unit/samples/bytes.bin'));
        $rom->save($tmp_file);

        $this->assertFileEquals($tmp_file, 'tests/Unit/samples/bytes.bin');

        unlink($tmp_file);
    }
}
