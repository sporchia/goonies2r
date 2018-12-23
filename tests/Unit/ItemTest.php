<?php

namespace Tests\Unit;

use App\Item;
use Tests\TestCase;

class ItemTest extends TestCase
{
    /**
     * Test that all the items exist properly.
     *
     * @return void
     *
     * @dataProvider itemPool
     */
    public function testStaticGet(string $name, int $item_id, int $item_gfx = null)
    {
        $item = Item::get($name);
        $this->assertEquals($item_id, $item->getByte());
        $this->assertEquals($item_gfx, $item->getGfxByte());
    }

    /**
     * Data for Static test.
     *
     * @return array
     */
    public function itemPool()
    {
        return [
            ['Nothing',     0x00, 0x00],
            ['DivingSuit',  0x01, 0x9b],
            ['Transceiver', 0x02, 0x9d],
            ['Glasses',     0x03, 0x96],
            ['Yo-Yo',       0x04, null],
            ['Ladder',      0x05, 0x9c],
            ['Hammer',      0x06, 0x93],
            ['Candle',      0x07, 0x98],
            ['Helmet',      0x08, 0x99],
            ['Raincoat',    0x09, 0x9a],
            ['HyperShoes',  0x0a, 0x94],
            ['JumpShoes',   0x0b, 0x9e],
            ['Vest',        0x0c, 0x95],
            ['KeysC0',      0xc0, 0xa0],
            ['KeysC1',      0xc1, 0xa0],
            ['KeysC2',      0xc2, 0xa0],
            ['KeysC3',      0xc3, 0xa0],
            ['BombsC4',     0xc4, 0xa1],
            ['BombsC5',     0xc5, 0xa1],
            ['BombsC6',     0xc6, 0xa1],
            ['BombsC7',     0xc7, 0xa1],
            ['FireboxC8',   0xc8, 0xa2],
            ['FireboxC9',   0xc9, 0xa2],
            ['FireboxCA',   0xca, 0xa2],
            ['FireboxCB',   0xcb, 0xa2],
            ['Boomerang',   0xcc, 0x97],
            ['Slingshot',   0xcd, 0xdf],
            ['DeviceF0',    0xf0, 0x9f],
            ['DeviceF1',    0xf1, 0x9f],
            ['DeviceF2',    0xf2, 0x9f],
            ['DeviceF3',    0xf3, 0x9f],
            ['DeviceF4',    0xf4, 0x9f],
            ['DeviceF5',    0xf5, 0x9f],
            ['GoonieE0',    0xe0, 0xa4],
            ['GoonieE1',    0xe1, 0xa3],
            ['GoonieE2',    0xe2, 0xa4],
            ['GoonieE3',    0xe3, 0xa4],
            ['GoonieE4',    0xe4, 0xa3],
            ['GoonieE5',    0xe5, 0xa4],
            ['Annie',       0xef, 0x07],
        ];
    }

    /**
     * Test that static get throws exception when item doesn't exist.
     *
     * @return void
     */
    public function testStaticGetException()
    {
        $this->expectException(\OutOfBoundsException::class);

        $item = Item::get('Item that Certainly doesn’t exist');
    }

    /**
     * Test translation for nice name.
     *
     * @return void
     */
    public function testI18nNiceName()
    {
        app('translator')->addLines([
            'item.GoonieNE' => 'Test Goonie',
        ], app()->getLocale());

        $item = new Item('GoonieNE', 0x01);

        $this->assertEquals('Test Goonie', $item->getNiceName());
    }

    /**
     * Test toString.
     *
     * @return void
     */
    public function testToString()
    {
        $item = new Item('name', 0x01);

        $this->assertEquals('name1', (string) $item);
    }
}
