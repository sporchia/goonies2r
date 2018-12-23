<?php

namespace App;

use App\Support\ItemCollection;

/**
 * An Item is any collectable thing in game.
 */
class Item
{
    /** @var string */
    protected $name;
    /** @var int */
    protected $byte;
    /** @var int|null */
    protected $gfx_byte;
    /** @var ItemCollection */
    protected static $items;

    /**
     * Get the Item by name.
     *
     * @param string $name Name of Item
     *
     * @throws \OutOfBoundsException if the Item doesn't exist
     *
     * @return \App\Item
     */
    public static function get(string $name) : self
    {
        $items = static::all();
        if (isset($items[$name])) {
            return $items[$name];
        }

        throw new \OutOfBoundsException('Unknown Item: ' . $name);
    }

    /**
     * Get the all known Items.
     *
     * @return \App\Support\ItemCollection
     */
    public static function all() : ItemCollection
    {
        if (isset(static::$items)) {
            return static::$items;
        }

        static::$items = new ItemCollection([
            new Item('Nothing',            0x00, 0x00), // may want to use 0x04 for yo-yo for now?
            new Item('DivingSuit',         0x01, 0x9b),
            new Item('Transceiver',        0x02, 0x9d),
            new Item('Glasses',            0x03, 0x96),
            new Item('Yo-Yo',              0x04, null), // you always have this
            new Item('Ladder',             0x05, 0x9c),
            new Item('Hammer',             0x06, 0x93),
            new Item('Candle',             0x07, 0x98), // in 0f with lady
            new Item('Helmet',             0x08, 0x99), // in 1d with lady
            new Item('Raincoat',           0x09, 0x9a),
            new Item('HyperShoes',         0x0a, 0x94),
            new Item('JumpShoes',          0x0b, 0x9e), // in 4a with frog
            new Item('Vest',               0x0c, 0x95),
            new Item\Key('KeysC0',         0xc0, 0xa0),
            new Item\Key('KeysC1',         0xc1, 0xa0),
            new Item\Key('KeysC2',         0xc2, 0xa0),
            new Item\Key('KeysC3',         0xc3, 0xa0),
            new Item('BombsC4',            0xc4, 0xa1),
            new Item('BombsC5',            0xc5, 0xa1),
            new Item('BombsC6',            0xc6, 0xa1),
            new Item('BombsC7',            0xc7, 0xa1),
            new Item('FireboxC8',          0xc8, 0xa2),
            new Item('FireboxC9',          0xc9, 0xa2),
            new Item('FireboxCA',          0xca, 0xa2),
            new Item('FireboxCB',          0xcb, 0xa2),
            new Item('Boomerang',          0xcc, 0x97),
            new Item('Slingshot',          0xcd, 0xdf),
            new Item('DeviceF0',           0xf0, 0x9f),
            new Item('DeviceF1',           0xf1, 0x9f),
            new Item('DeviceF2',           0xf2, 0x9f),
            new Item('DeviceF3',           0xf3, 0x9f),
            new Item('DeviceF4',           0xf4, 0x9f),
            new Item('DeviceF5',           0xf5, 0x9f),
            new Item\Goonie('GoonieE0',    0xe0, 0xa4),
            new Item\Goonie('GoonieE1',    0xe1, 0xa3),
            new Item\Goonie('GoonieE2',    0xe2, 0xa4),
            new Item\Goonie('GoonieE3',    0xe3, 0xa4),
            new Item\Goonie('GoonieE4',    0xe4, 0xa3),
            new Item\Goonie('GoonieE5',    0xe5, 0xa4),
            new Item\Goonie('Annie',       0xef, 0x07),
            new Item\Hint('Hint 1',        0x4c, null),
            new Item\Hint('Hint 2',        0x4e, null),
            new Item\Hint('Hint 3',        0x52, null),
            new Item\Hint('Hint 4',        0x54, null),
            new Item\Hint('Hint 5',        0x53, null),
            new Item\Hint('Hint 6',        0x50, null),
            new Item\Hint('Hint 7',        0x4f, null),
            new Item\Hint('Hint 8',        0x51, null),
            new Item\Hint('Hint 9',        0x4d, null),
            new Item\Hint('Hint 10',       0x55, null),
        ]);

        return static::all();
    }

    /**
     * Create a new Item.
     *
     * @param string $name Unique name of item
     * @param int $byte data to write to Location addresses
     * @param int|null $gfx_byte data to write to Location addresses
     *
     * @return void
     */
    public function __construct(string $name, int $byte, int $gfx_byte = null)
    {
        $this->name = $name;
        $this->byte = $byte;
        $this->gfx_byte = $gfx_byte;
    }

    /**
     * Get the name of this Item.
     *
     * @return string
     */
    public function getName() : string
    {
        return $this->name;
    }

    /**
     * Get the nice name of this Item.
     *
     * @return string
     */
    public function getNiceName() : string
    {
        $nice_name = __("item.$this->name");

        return is_string($nice_name) ? $nice_name : $this->name;
    }

    /**
     * Get the item byte to write.
     *
     * @return int
     */
    public function getByte() : int
    {
        return $this->byte;
    }

    /**
     * Get the item gfx byte to write.
     *
     * @return int
     */
    public function getGfxByte() : ?int
    {
        return $this->gfx_byte;
    }

    /**
     * serialized version of Item.
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->name . $this->byte;
    }
}
