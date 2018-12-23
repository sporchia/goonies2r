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
     *
     * @return void
     */
    public function __construct(Graph $graph, $name, int $room_id, int $interaction = 0x00, int $exits = 0x00, int $palette = 0x0c)
    {
        parent::__construct($graph, sprintf('Room %02x', $room_id));

        $this->name = $name;
        $this->room_id = $room_id;
        $this->meta = [
            'hidden_safe' => ($interaction & 0x03) == 0x00,
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
     * remodel a Room for a goonie.
     *
     * @param \App\Rom $rom rom to call remodel functons on
     * @param int      $id  offset for the remodel so we don't remodel the same room 2x
     *
     * @return void
     */
    public function remodelForGoonie(Rom $rom, int $id) : void
    {
        // skip remodel for Annie
        if ($this->meta['palette'] === 0x10) {
            return;
        }
        $rom->cloneRoomWithCage($this->room_id, $id, $this->meta['palette']);
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
}
