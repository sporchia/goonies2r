<?php

namespace App\Location;

use App\Location;
use App\Rom;
use App\Room;
use Fhaculty\Graph\Graph;

/**
 * A Location is any place an Item can be found in game.
 */
class PunchNPC extends Location
{
    /**
     * {@inheritdoc}
     */
    public function __construct(Graph $graph, $id, Room $room)
    {
        parent::__construct($graph, $id, $room);

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
     * Write the Item to this Location in ROM.
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

        $item_byte = $this->item->getByte();
        $rom->write(0x6210, pack('C', $item_byte));
        $item_gfx_btye = $this->item->getGfxByte();
        $rom->write(0x6215, pack('C', $item_gfx_btye));

        return $this;
    }
}
