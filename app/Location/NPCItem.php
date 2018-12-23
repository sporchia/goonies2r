<?php

namespace App\Location;

use App\Location;
use App\Rom;
use App\Room;
use Fhaculty\Graph\Graph;

/**
 * A Location is any place an Item can be found in game.
 */
class NPCItem extends Location
{
    /** @var int */
    protected $npc_offset;

    /**
     * {@inheritdoc}
     */
    public function __construct(Graph $graph, $id, Room $room, int $npc_offset = 0x00)
    {
        parent::__construct($graph, $id, $room);

        $this->npc_offset = $npc_offset;

        $this->setAttribute('graphviz.fillcolor', 'lightcyan');
        $this->setAttribute('graphviz.style', 'filled');
    }

    /**
     * {@inheritdoc}
     */
    public function createEdgeToRoom() : void
    {
        parent::createEdgeToRoom();

        $this->edge->setAttribute('graphviz.color', 'cyan');
    }

    /**
     * Write the Item to this Location in ROM. Will set Item if passed in, and only write if there is an Item set.
     *
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

        $rom->write($room_offset + 3, pack('C', 0x8a + $this->npc_offset));

        $item_byte = $this->item->getByte();
        $rom->write(0x5f02 + $this->npc_offset, pack('C', $item_byte));
        $item_gfx_btye = $this->item->getGfxByte();
        $rom->write(0x5f00 + $this->npc_offset, pack('C', $item_gfx_btye));

        return $this;
    }
}
