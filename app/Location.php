<?php

namespace App;

use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;

/**
 * A Location is any place an Item can be found in game
 */
class Location extends Vertex
{
    /** @var string|int */
    protected $name;
    /** @var \App\Room */
    protected $room;
    /** @var \App\Item|null */
    protected $item = null;
    /** @var \Fhaculty\Graph\Edge\Base */
    protected $edge;

    /**
     * Create a new Location
     *
     * @param \Fhaculty\Graph\Graph $graph        graph to be added to
     * @param string|int            $id           identifier used to uniquely identify this vertex in the graph
     * @param \App\Room             $room         Room containing this location
     *
     * @return void
     */
    public function __construct(Graph $graph, $id, Room $room)
    {
        parent::__construct($graph, $id);

        $this->name = $id;
        $this->room = $room;

        $this->setAttribute('graphviz.color', 'green');
    }

    /**
     * Create the edge from this location to it's room
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
    public function setRoom(Room $room) : Location
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

    /**
     * Sets the item for this location.
     *
     * @param \App\Item|null $item Item to be placed at this Location
     *
     * @throws \Exception if Item is not valid for this Location type
     *
     * @return $this
     */
    public function setItem(Item $item = null) : Location
    {
        if ($item instanceof Item\Goonie || $item instanceof Item\Hint) {
            throw new \Exception('Invalid Item assignment');
        }

        $this->item = $item;

        $this->setAttribute('graphviz.label', $item === null ? $this->name : $item->getNiceName());

        return $this;
    }

    /**
     * Does this Location have (a particular) Item assigned
     *
     * @param \App\Item|null $item item to search locations for
     *
     * @return bool
     */
    public function hasItem(Item $item = null) : bool
    {
        return $item ? $this->item == $item : $this->item !== null;
    }

    /**
     * Get the Item assigned to this Location, null is nothing is assigned
     *
     * @return \App\Item|null
     */
    public function getItem() : ?Item
    {
        return $this->item;
    }

    /**
     * Write the Item to this Location in ROM. Will set Item if passed in, and only write if there is an Item set.
     * @TODO: this is side-affecty
     *
     * @param \App\Rom $rom interface we are going to write to
     *
     * @throws \Exception if no item is set for location
     *
     * @return $this
     */
    public function writeItem(Rom $rom) : Location
    {
        if (!$this->item) {
            throw new \Exception('No Item set to be written');
        }

        $room_offset = $this->room->getRoomId() * 4 + Rom::ROOM_DATA_OFFSET;

        $item_byte = $this->item->getByte();
        $rom->write($room_offset + 3, pack('C', $item_byte));
        $item_gfx_btye = $this->item->getGfxByte();
        $rom->write($room_offset + 1, pack('C', $item_gfx_btye));

        return $this;
    }

    /**
     * Get the name of this Location.
     *
     * @return string
     */
    public function getName() : string
    {
        return (string) $this->name;
    }

    /**
     * Convert this to string representation
     *
     * @return string
     */
    public function __toString() : string
    {
        return $this->getName();
    }
}
