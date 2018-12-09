<?php

namespace Tests\Unit;

use App\Rom;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RomTest extends TestCase
{
    /**
     * @return void
     */
    public function testReadFile()
    {
        $rom = new Rom(base_path('tests/Unit/samples/bytes.bin'));

        $this->assertEquals([0x03, 0x04, 0x05], $rom->read(0x03, 0x03));
    }

    /**
     * @return void
     */
    public function testLoadFileThrowsOnBadFile()
    {
        $this->expectException(\Exception::class);

        $rom = new Rom(base_path('tests/Unit/samples/thisfilecertainlydoesntexist.bin'));
    }

    /**
     * @return void
     */
    public function testWriteLogEmptyBeforeWrites()
    {
        $rom = new Rom;

        $this->assertEquals([], $rom->getWriteLog());
    }

    /**
     * @return void
     */
    public function testCloneRoomWithCageWithSmallFile()
    {
        $this->expectException(\Exception::class);

        $rom = new Rom(base_path('tests/Unit/samples/bytes.bin'));

        $rom->cloneRoomWithCage(1, 1, 0x0c);
    }

    /**
     * @return void
     */
    public function testRemodelOldCageWithSmallFile()
    {
        $this->expectException(\Exception::class);

        $rom = new Rom(base_path('tests/Unit/samples/bytes.bin'));

        $rom->remodelOldCageRooms();
    }

    /**
     * @return void
     */
    public function testWriteLog()
    {
        $rom = new Rom;

        $rom->write(0x00, 'hello');

        $this->assertEquals([[0 => [0x68, 0x65, 0x6c, 0x6c, 0x6f]]], $rom->getWriteLog());
    }

    /**
     * @return void
     */
    public function testSaveFile()
    {
        $tmp_file = tempnam(sys_get_temp_dir(), __CLASS__);
        $rom = new Rom(base_path('tests/Unit/samples/bytes.bin'));
        $rom->save($tmp_file);

        $this->assertFileEquals($tmp_file, 'tests/Unit/samples/bytes.bin');

        unlink($tmp_file);
    }
}
