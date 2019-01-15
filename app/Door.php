<?php

namespace App;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;

/**
 * Doors connecting the Regions to Rooms.
 */
class Door extends Vertex
{
    /** @var int */
    protected $door_id;
    /** @var \App\Room */
    protected $room;
    /** @var array */
    public $meta;
    /** @var \Fhaculty\Graph\Edge\Base */
    protected $edge;

    /**
     * Create a new Over World Door.
     *
     * @param \Fhaculty\Graph\Graph $graph        graph to be added to
     * @param int                   $door_id      ID of room containing this location
     * @param int                   $interaction  interaction flags
     * @param int                   $map_location nametable address to mark when this door is used for something
     * @param \App\Room             $room         Room containing this location
     *
     * @return void
     */
    public function __construct(Graph $graph, int $door_id, int $interaction, int $map_location, Room $room)
    {
        parent::__construct($graph, sprintf('Door %02x', $door_id));

        $this->door_id = $door_id;
        $this->meta = [
            'interaction' => $interaction,
            'map_address' => $map_location & 0xffff,
            'side' => (($map_location >> 16) & 1) ? 'back' : 'front',
        ];

        $this->setAttribute('graphviz.color', 'red');

        $this->room = $room;
    }

    /**
     * Get the Door Id.
     *
     * @return int
     */
    public function getDoorId() : int
    {
        return $this->door_id;
    }

    /**
     * Create the edge from this location to it's room.
     *
     * @return void
     */
    public function createEdgeToRoom() : void
    {
        $this->edge = $this->createEdge($this->room);
    }

    /**
     * Sets the room for this location.
     *
     * @param \App\Room $room Room to be associated
     *
     * @return $this
     */
    public function setRoom(Room $room) : Door
    {
        $this->room = $room;

        return $this;
    }

    /**
     * Get the room for this location.
     *
     * @return \App\Room
     */
    public function getRoom(Item $item = null) : Room
    {
        return $this->room;
    }
}
