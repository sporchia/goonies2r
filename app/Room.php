<?php

namespace App;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;

/**
 * Rooms you can navigate through.
 */
class Room extends Vertex
{
    /** @var string|int */
    protected $name;
    /** @var int */
    protected $room_id;
    /** @var array */
    public $meta;

    /**
     * Create a new Room.
     *
     * @param \Fhaculty\Graph\Graph $graph       graph to be added to
     * @param string|int            $name        identifier used to uniquely identify this vertex in the graph
     * @param int                   $room_id     ID of room containing this location
     * @param int                   $interaction interaction flags
     * @param int                   $exits       0x01 right, 0x02 left, 0x04 down, 0x08 up, 0x40 ladder down, 0x80 ladder up
     * @param int                   $palette     Which PPU block to load for room
     *
     * @return void
     */
    public function __construct(Graph $graph, $name, int $room_id, int $interaction = 0x00, int $exits = 0x00, int $palette = 0x0c)
    {
        parent::__construct($graph, sprintf('Room %02x', $room_id));

        $this->name = $name;
        $this->room_id = $room_id;
        $this->meta = [
            'hidden_up' => ($interaction & 0x03) == 0x01,
            'hidden_door' => ($interaction & 0x03) == 0x02,
            'hidden_down' => ($interaction & 0x03) == 0x03,
            'item_glasses' => $interaction >> 2 & 1,
            'hidden_item' => $interaction >> 3 & 1,
            'dark' => $interaction >> 4 & 1,
            'item_hammer' => $interaction >> 5 & 1,
            'item_punch' => $interaction >> 6 & 1,
            'item_visible' => $interaction >> 7 & 1,
            'right' => $exits >> 0 & 1,
            'left' => $exits >> 1 & 1,
            'down' => $exits >> 2 & 1,
            'up' => $exits >> 3 & 1,
            'ladder_down' => $exits >> 6 & 1,
            'ladder_up' => $exits >> 7 & 1,
            'palette' => $palette,
            'safe' => false,
            'water' => false,
            'cage' => false,
        ];

        if ($this->meta['dark']) {
            $this->setAttribute('graphviz.fillcolor', 'gray');
            $this->setAttribute('graphviz.style', 'filled');
        }
    }

    /**
     * Determine if we can even put a Goonie in this room, currently we don't move Annie.
     *
     * @return bool
     */
    public function canHoldGoonie() : bool
    {
        return !$this->meta['item_glasses']
            && !$this->meta['item_hammer']
            && !$this->meta['item_punch']
            && !$this->meta['item_visible']
            && !$this->meta['up']
            && $this->name !== 'Annie';
    }

    /**
     * Get the Room Id.
     *
     * @return int
     */
    public function getRoomId() : int
    {
        return $this->room_id;
    }

    /**
     * Set if there is a safe in the room.
     *
     * @param bool $safe whether or not there is a safe in here
     *
     * @return void
     */
    public function setSafe(bool $safe = false) : void
    {
        $this->meta['safe'] = $safe;
    }

    /**
     * Set if there is a cage in the room.
     *
     * @param bool $cage whether or not there is a cage in here
     *
     * @return void
     */
    public function setCage(bool $cage = false) : void
    {
        $this->meta['cage'] = $cage;
    }

    /**
     * Set if there is a water access in the room.
     *
     * @param bool $water whether or not there is a water hole in here
     *
     * @return void
     */
    public function setWater(bool $water = false) : void
    {
        $this->meta['water'] = $water;
    }

    /**
     * Write the Layout for this Room in the ROM.
     *
     * @param \App\Rom $rom interface we are going to write to
     *
     * @return void
     */
    public function writeToRom(Rom $rom) : void
    {
        $room_layout_offset = $this->getRoomId() * 16 + Rom::ROOM_LAYOUT_OFFSET;
        $room_ppu_offset = $this->getRoomId() + Rom::ROOM_PPU_OFFSET;

        $rom->write($room_layout_offset, pack('C*', ...$this->getLayout()));
        $rom->write($room_ppu_offset, pack('C', $this->meta['palette']));
    }

    public function getLayout() : array
    {
        switch ($this->meta['palette']) {
            case 0x0c: return $this->buildLayout0c();
            case 0x0d: return $this->buildLayout0d();
            case 0x0e: return $this->buildLayout0e();
            case 0x0f: return $this->buildLayout0f();
            case 0x10: return $this->buildLayout10();
        }
    }

    private function buildLayout0c() : array
    {
        $layout = [
            0x0a, 0x0c, 0x0c, 0x0b,
            0x0c, 0x10, 0x11, 0x0c,
            0x0c, 0x14, 0x15, 0x0c,
            0x0e, 0x0d, 0x0d, 0x0f,
        ];

        if ($this->meta['left']) {
            $layout[4] = 0x12;
            $layout[8] = 0x16;
            $layout[12] = 0x1a;
        }

        if ($this->meta['right']) {
            $layout[7] = 0x13;
            $layout[11] = 0x17;
            $layout[15] = 0x1b;
        }

        if ($this->meta['up']) {
            $layout[9] = 0x00;
            $layout[10] = 0x01;
        }
        if ($this->meta['hidden_door']) {
            $layout[9] = 0xd7;
            $layout[10] = 0xd8;
            if ($this->meta['item_glasses']) {
                $this->meta['palette'] = 0x14;
                $layout[9] = 0xbb;
                $layout[10] = 0xbc;
            }
        }

        if ($this->meta['safe']) {
            $layout[9] = 0x02;
            $layout[10] = 0x03;
            if ($this->meta['item_hammer']) {
                $layout[9] = 0xd7;
                $layout[10] = 0xd8;
            }
            if ($this->meta['item_glasses']) {
                $this->meta['palette'] = 0x14;
                $layout[9] = 0xb7;
                $layout[10] = 0xb8;
            }
        }

        if ($this->meta['ladder_up']) {
            $layout[1] = 0x04;
            $layout[2] = 0x05;
            if ($this->meta['hidden_up']) {
                $layout[1] = 0xbd;
                $layout[2] = 0xbe;
            }
        }

        if ($this->meta['ladder_down']) {
            $layout[13] = 0x08;
            $layout[14] = 0x09;
            if ($this->meta['hidden_down']) {
                $layout[13] = 0xb9;
                $layout[14] = 0xba;
            }
        }

        if ($this->meta['cage']) {
            $layout[5] = 0x18;
            $layout[6] = 0x19;
            $layout[9] = 0x1c;
            $layout[10] = 0x1d;
        }

        return $layout;
    }

    private function buildLayout0d() : array
    {
        $layout = [
            0x2e, 0x31, 0x31, 0x2f,
            0x31, 0x34, 0x35, 0x31,
            0x31, 0x38, 0x39, 0x31,
            0x32, 0x30, 0x30, 0x33,
        ];

        if ($this->meta['left']) {
            $layout[4] = 0x36;
            $layout[8] = 0x3a;
            $layout[12] = 0x3e;
        }

        if ($this->meta['right']) {
            $layout[7] = 0x37;
            $layout[11] = 0x3b;
            $layout[15] = 0x3f;
        }

        if ($this->meta['up']) {
            $layout[9] = 0x24;
            $layout[10] = 0x25;
        }
        if ($this->meta['hidden_door']) {
            $layout[9] = 0xd9;
            $layout[10] = 0xda;
            if ($this->meta['item_glasses']) {
                $this->meta['palette'] = 0x15;
                $layout[9] = 0xc3;
                $layout[10] = 0xc4;
            }
        }

        if ($this->meta['safe']) {
            $layout[9] = 0x26;
            $layout[10] = 0x27;
            if ($this->meta['item_hammer']) {
                $layout[9] = 0xd9;
                $layout[10] = 0xda;
            }
            if ($this->meta['item_glasses']) {
                $this->meta['palette'] = 0x15;
                $layout[9] = 0xbf;
                $layout[10] = 0xc0;
            }
        }

        if ($this->meta['ladder_up']) {
            $layout[1] = 0x28;
            $layout[2] = 0x29;
            if ($this->meta['hidden_up']) {
                $layout[1] = 0xc5;
                $layout[2] = 0xc6;
            }
        }

        if ($this->meta['ladder_down']) {
            $layout[13] = 0x2c;
            $layout[14] = 0x2d;
            if ($this->meta['hidden_down']) {
                $layout[13] = 0xc1;
                $layout[14] = 0xc2;
            }
        }

        if ($this->meta['water']) {
            $layout[13] = 0x42;
            $layout[14] = 0x43;
        }

        if ($this->meta['cage']) {
            $layout[5] = 0x3c;
            $layout[6] = 0x3d;
            $layout[9] = 0x40;
            $layout[10] = 0x41;
        }

        return $layout;
    }

    private function buildLayout0e() : array
    {
        $layout = [
            0x56, 0x57, 0x58, 0x59,
            0x5a, 0x5b, 0x5c, 0x5d,
            0x5e, 0x5f, 0x60, 0x61,
            0x62, 0x63, 0x64, 0x65,
        ];

        if ($this->meta['left']) {
            $layout[4] = 0x4c;
            $layout[8] = 0x50;
            $layout[12] = 0x54;
        }

        if ($this->meta['right']) {
            $layout[7] = 0x4d;
            $layout[11] = 0x51;
            $layout[15] = 0x55;
        }

        if ($this->meta['up']) {
            $layout[9] = 0x46;
            $layout[10] = 0x47;
        }
        if ($this->meta['hidden_door']) {
            $layout[9] = 0xdb;
            $layout[10] = 0xdc;
            if ($this->meta['item_glasses']) {
                $this->meta['palette'] = 0x16;
                $layout[9] = 0xcb;
                $layout[10] = 0xcc;
            }
        }

        if ($this->meta['safe']) {
            $layout[9] = 0x48;
            $layout[10] = 0x49;
            if ($this->meta['item_hammer']) {
                $layout[9] = 0xdb;
                $layout[10] = 0xdc;
            }
            if ($this->meta['item_glasses']) {
                $this->meta['palette'] = 0x16;
                $layout[9] = 0xc7;
                $layout[10] = 0xc8;
            }
        }

        if ($this->meta['ladder_up']) {
            $layout[1] = 0x4a;
            $layout[2] = 0x4b;
            if ($this->meta['hidden_up']) {
                $layout[1] = 0xcd;
                $layout[2] = 0xce;
            }
        }

        if ($this->meta['ladder_down']) {
            $layout[13] = 0x4e;
            $layout[14] = 0x4f;
            if ($this->meta['hidden_down']) {
                $layout[13] = 0xc9;
                $layout[14] = 0xca;
            }
        }

        if ($this->meta['cage']) {
            $layout[5] = 0x66;
            $layout[6] = 0x67;
            $layout[9] = 0x68;
            $layout[10] = 0x69;
        }

        return $layout;
    }

    private function buildLayout0f() : array
    {
        $layout = [
            0x6a, 0x6b, 0x6c, 0x6d,
            0x6e, 0x6f, 0x70, 0x71,
            0x72, 0x73, 0x74, 0x75,
            0x76, 0x77, 0x78, 0x79,
        ];

        if ($this->meta['left']) {
            $layout[4] = 0x80;
            $layout[8] = 0x84;
            $layout[12] = 0x88;
        }

        if ($this->meta['right']) {
            $layout[7] = 0x81;
            $layout[11] = 0x85;
            $layout[15] = 0x89;
        }

        if ($this->meta['up']) {
            $layout[9] = 0x8c;
            $layout[10] = 0x8d;
        }
        if ($this->meta['hidden_door']) {
            $layout[9] = 0xdd;
            $layout[10] = 0xde;
            if ($this->meta['item_glasses']) {
                $this->meta['palette'] = 0x17;
                $layout[9] = 0xd3;
                $layout[10] = 0xd4;
            }
        }

        if ($this->meta['safe']) {
            $layout[9] = 0x7a;
            $layout[10] = 0x7b;
            // open safe
            // $layout[9] = 0x7c;
            // $layout[10] = 0x7d;
            if ($this->meta['item_hammer']) {
                $layout[9] = 0xdd;
                $layout[10] = 0xde;
            }
            if ($this->meta['item_glasses']) {
                $this->meta['palette'] = 0x17;
                $layout[9] = 0xcf;
                $layout[10] = 0xd0;
            }
        }

        if ($this->meta['ladder_up']) {
            $layout[1] = 0x7e;
            $layout[2] = 0x7f;
            if ($this->meta['hidden_up']) {
                $layout[1] = 0xd5;
                $layout[2] = 0xd6;
            }
        }

        if ($this->meta['ladder_down']) {
            $layout[13] = 0x82;
            $layout[14] = 0x83;
            if ($this->meta['hidden_down']) {
                $layout[13] = 0xd1;
                $layout[14] = 0xd2;
            }
        }

        if ($this->meta['water']) {
            $layout[13] = 0x86;
            $layout[14] = 0x87;
        }

        if ($this->meta['cage']) {
            $layout[5] = 0x8a;
            $layout[6] = 0x8b;
            $layout[9] = 0x8e;
            $layout[10] = 0x8f;
        }

        return $layout;
    }

    private function buildLayout10() : array
    {
        return [
            0x9a, 0x9b, 0xa6, 0xa5,
            0x9e, 0x90, 0x91, 0xa4,
            0x9f, 0x94, 0x95, 0xa3,
            0xa0, 0xa1, 0xa6, 0xa2,
        ];
    }
}
