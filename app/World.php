<?php

namespace App;

use App\Support\Collection;
use App\Support\ItemCollection;
use App\Support\LocationCollection;
use Fhaculty\Graph\Graph;
use Fhaculty\Graph\Vertex;
use Graphp\Algorithms\Search\BreadthFirst;

/**
 * this is a standard world, no rooms moved around, items will stay in item locations.
 */
class World
{
    /** @var \Fhaculty\Graph\Graph */
    protected $graph;
    /** @var array */
    protected $vertices = [];
    /** @var \App\Support\LocationCollection */
    protected $locations;

    /**
     * Create a graph of the current world based on Items Collected.
     *
     * @param \App\Support\ItemCollection $items items that player has collected
     *
     * @return void
     */
    public function __construct(ItemCollection $items)
    {
        $this->graph = new Graph;
        $start = new Vertex($this->graph, 'Start');
        $start->setAttribute('graphviz.fillcolor', 'green');
        $start->setAttribute('graphviz.style', 'filled');

        // rooms
        $this->vertices = array_merge($this->vertices, [
            'Start'   => $start,

            'Front - Attic'               => new Region($this->graph, 'Front - Attic'),
            'Front - Orange House Left'   => new Region($this->graph, 'Front - Orange House Left'),
            'Front - Orange House Right'  => new Region($this->graph, 'Front - Orange House Right'),
            'Front - Gray Basement Left'  => new Region($this->graph, 'Front - Gray Basement Left'),
            'Front - Gray Basement Right' => new Region($this->graph, 'Front - Gray Basement Right'),
            'Front - Orange Caves Left'   => new Region($this->graph, 'Front - Orange Caves Left'),
            'Front - Orange Caves Right'  => new Region($this->graph, 'Front - Orange Caves Right'),
            'Front - Ice Caves Left'      => new Region($this->graph, 'Front - Ice Caves Left'),
            'Front - Ice Caves Right'     => new Region($this->graph, 'Front - Ice Caves Right'),
            'Front - Aquarium Left'       => new Region($this->graph, 'Front - Aquarium Left'),
            'Front - Aquarium Right'      => new Region($this->graph, 'Front - Aquarium Right'),
            'Front - Bridge'              => new Region($this->graph, 'Front - Bridge'),
            'Front - Green Cabin'         => new Region($this->graph, 'Front - Green Cabin'),
            'Front - Brown Castle'        => new Region($this->graph, 'Front - Brown Castle'),
            'Front - Purple Caves'        => new Region($this->graph, 'Front - Purple Caves'),
            'Front - Lava Pit'            => new Region($this->graph, 'Front - Lava Pit'),
            'Back - Gray House Left'      => new Region($this->graph, 'Back - Gray House Left'),
            'Back - Gray House Right'     => new Region($this->graph, 'Back - Gray House Right'),
            'Back - Gray Basement Left'   => new Region($this->graph, 'Back - Gray Basement Left'),
            'Back - Gray Basement Right'  => new Region($this->graph, 'Back - Gray Basement Right'),
            'Back - Red Caves'            => new Region($this->graph, 'Back - Red Caves'),
            'Back - Aquarium Left'        => new Region($this->graph, 'Back - Aquarium Left'),
            'Back - Aquarium Right'       => new Region($this->graph, 'Back - Aquarium Right'),
            'Back - Orange Cabin'         => new Region($this->graph, 'Back - Orange Cabin'),
            'Back - Red Castle'           => new Region($this->graph, 'Back - Red Castle'),
            'Back - Green Caves'          => new Region($this->graph, 'Back - Green Caves'),

            'Room 00' => new Room($this->graph, 'Hammer',            0x00, 0x80, 0x04, 0x0c),
            'Room 01' => new Room($this->graph, 'Keys 1',            0x01, 0x80, 0x0c, 0x0c),
            'Room 02' => new Room($this->graph, 'Hint 1',            0x02, 0x00, 0x84, 0x0c),
            'Room 03' => new Room($this->graph, 'Slingshot',         0x03, 0x80, 0x0c, 0x0d),
            'Room 04' => new Room($this->graph, 'Need Glasses Lady', 0x04, 0x00, 0x04, 0x0d),
            'Room 05' => new Room($this->graph, 'Device 1',          0x05, 0x80, 0x0c, 0x0d),
            'Room 06' => new Room($this->graph, 'Warp Zone 1',       0x06, 0x00, 0x0c, 0x0d),
            'Room 07' => new Room($this->graph, 'Bomb Box 1',        0x07, 0x40, 0x05, 0x0c),
            'Room 08' => new Room($this->graph, 'Connector',         0x08, 0x6a, 0x02, 0x0c),
            'Room 09' => new Room($this->graph, 'Goonie 1',          0x09, 0x00, 0x04, 0x0c),
            'Room 0a' => new Room($this->graph, 'Old Lady',          0x0a, 0x00, 0x04, 0x0e),
            'Room 0b' => new Room($this->graph, 'Connector',         0x0b, 0x00, 0x07, 0x0d),
            'Room 0c' => new Room($this->graph, 'Hint 2',            0x0c, 0x00, 0x01, 0x0d),
            'Room 0d' => new Room($this->graph, 'Fire Box 1',        0x0d, 0x40, 0x02, 0x0d),
            'Room 0e' => new Room($this->graph, 'Transicer Room',    0x0e, 0x40, 0x0d, 0x0e),
            'Room 0f' => new Room($this->graph, 'Candle Lady',       0x0f, 0x00, 0x02, 0x0e),
            'Room 10' => new Room($this->graph, 'Connector',         0x10, 0x10, 0x0c, 0x0e),
            'Room 11' => new Room($this->graph, 'Connector',         0x11, 0x10, 0x0c, 0x0d),
            'Room 12' => new Room($this->graph, 'Glasses',           0x12, 0x68, 0x07, 0x0d),
            'Room 13' => new Room($this->graph, 'Connector',         0x13, 0x00, 0x01, 0x0d),
            'Room 14' => new Room($this->graph, 'Boomerang',         0x14, 0x80, 0x0a, 0x0d),
            'Room 15' => new Room($this->graph, 'Eskimo',            0x15, 0x00, 0x04, 0x0d),
            'Room 16' => new Room($this->graph, 'Connector',         0x16, 0x00, 0x0c, 0x0d),
            'Room 17' => new Room($this->graph, 'Hint 3',            0x17, 0x00, 0x05, 0x0e),
            'Room 18' => new Room($this->graph, 'Connector',         0x18, 0x6a, 0x02, 0x0e),
            'Room 19' => new Room($this->graph, 'Goonie 2',          0x19, 0x00, 0x04, 0x0d),
            'Room 1a' => new Room($this->graph, 'Connector',         0x1a, 0x00, 0x06, 0x0e),
            'Room 1b' => new Room($this->graph, 'Connector',         0x1b, 0x00, 0x09, 0x0e),
            'Room 1c' => new Room($this->graph, 'Connector',         0x1c, 0x1e, 0x04, 0x0e),
            'Room 1d' => new Room($this->graph, 'Helmet Lady',       0x1d, 0x00, 0x04, 0x0e),
            'Room 1e' => new Room($this->graph, 'Connector',         0x1e, 0x10, 0x0d, 0x0e),
            'Room 1f' => new Room($this->graph, 'Konamiman',         0x1f, 0x00, 0x05, 0x0e),
            'Room 20' => new Room($this->graph, 'Device 2',          0x20, 0x90, 0x0a, 0x0e),
            'Room 21' => new Room($this->graph, 'Keys 2',            0x21, 0x50, 0x06, 0x0e),
            'Room 22' => new Room($this->graph, 'Connector',         0x22, 0x0e, 0x06, 0x0e),
            'Room 23' => new Room($this->graph, 'Connector',         0x23, 0x00, 0x09, 0x0e),
            'Room 24' => new Room($this->graph, 'Warp Man',          0x24, 0x00, 0x0c, 0x0e),
            'Room 25' => new Room($this->graph, 'Fire Box 2',        0x25, 0x40, 0x04, 0x0e),
            'Room 26' => new Room($this->graph, 'Connector',         0x26, 0x6a, 0x04, 0x0e),
            'Room 27' => new Room($this->graph, 'Connector',         0x27, 0x00, 0x0c, 0x0e),
            'Room 28' => new Room($this->graph, 'Connector',         0x28, 0x00, 0x0c, 0x0e),
            'Room 29' => new Room($this->graph, 'Hint 5',            0x29, 0x10, 0x06, 0x0e),
            'Room 2a' => new Room($this->graph, 'Device 3',          0x2a, 0x80, 0x01, 0x0e),
            'Room 2b' => new Room($this->graph, 'Connector',         0x2b, 0x00, 0x0c, 0x0e),
            'Room 2c' => new Room($this->graph, 'Connector',         0x2c, 0x6b, 0x46, 0x0e),
            'Room 2d' => new Room($this->graph, 'Connector',         0x2d, 0x00, 0x09, 0x0e),
            'Room 2e' => new Room($this->graph, 'Old Lady',          0x2e, 0x00, 0x04, 0x0e),
            'Room 2f' => new Room($this->graph, 'Connector',         0x2f, 0x69, 0x81, 0x0e),
            'Room 30' => new Room($this->graph, 'Connector',         0x30, 0x00, 0x0a, 0x0e),
            'Room 31' => new Room($this->graph, 'Connector',         0x31, 0x00, 0x0d, 0x0e),
            'Room 32' => new Room($this->graph, 'Connector',         0x32, 0x00, 0x0b, 0x0e),
            'Room 33' => new Room($this->graph, 'Hint 6',            0x33, 0x00, 0x02, 0x0e),
            'Room 34' => new Room($this->graph, 'Hint 7',            0x34, 0x00, 0x05, 0x0e),
            'Room 35' => new Room($this->graph, 'Connector',         0x35, 0x0e, 0x06, 0x0e),
            'Room 36' => new Room($this->graph, 'Goonie 4',          0x36, 0x00, 0x04, 0x0e),
            'Room 37' => new Room($this->graph, 'Connector',         0x37, 0x00, 0x05, 0x0e),
            'Room 38' => new Room($this->graph, 'Connector',         0x38, 0x6a, 0x03, 0x0e),
            'Room 39' => new Room($this->graph, 'Keys 3',            0x39, 0x40, 0x04, 0x0e),
            'Room 3a' => new Room($this->graph, 'Raincoat',          0x3a, 0x68, 0x04, 0x0c),
            'Room 3b' => new Room($this->graph, 'Connector',         0x3b, 0x00, 0x0c, 0x0e),
            'Room 3c' => new Room($this->graph, 'Fire Box 4',        0x3c, 0x40, 0x04, 0x0e),
            'Room 3d' => new Room($this->graph, 'Connector',         0x3d, 0x00, 0x0e, 0x0e),
            'Room 3e' => new Room($this->graph, 'Hint 8',            0x3e, 0x00, 0x06, 0x0e),
            'Room 3f' => new Room($this->graph, 'Connector',         0x3f, 0x00, 0x09, 0x0e),
            'Room 40' => new Room($this->graph, 'Connector',         0x40, 0x6a, 0x05, 0x0e),
            'Room 41' => new Room($this->graph, 'Diving Suit',       0x41, 0x0c, 0x04, 0x0e),
            'Room 42' => new Room($this->graph, 'Connector',         0x42, 0x00, 0x0d, 0x0e),
            'Room 43' => new Room($this->graph, 'Connector',         0x43, 0x00, 0x05, 0x0e),
            'Room 44' => new Room($this->graph, 'Hint 9',            0x44, 0x00, 0x06, 0x0e),
            'Room 45' => new Room($this->graph, 'Bomb Box 3',        0x45, 0x40, 0x0a, 0x0e),
            'Room 46' => new Room($this->graph, 'Device 5',          0x46, 0x80, 0x01, 0x0f),
            'Room 47' => new Room($this->graph, 'Keys 3',            0x47, 0x40, 0x02, 0x0f),
            'Room 48' => new Room($this->graph, 'Hint 4',            0x48, 0x00, 0x00, 0x0f),
            'Room 49' => new Room($this->graph, 'Connector',         0x49, 0x00, 0x02, 0x0f),
            'Room 4a' => new Room($this->graph, 'Jumping Shoes',     0x4a, 0x00, 0x01, 0x0f),
            'Room 4b' => new Room($this->graph, 'Ladder',            0x4b, 0x40, 0x03, 0x0f),
            'Room 4c' => new Room($this->graph, 'Bomb Box 2',        0x4c, 0x40, 0x01, 0x0f),
            'Room 4d' => new Room($this->graph, 'Connector',         0x4d, 0x69, 0x82, 0x0f),
            'Room 4e' => new Room($this->graph, 'Goonie 3',          0x4e, 0x00, 0x40, 0x0f),
            'Room 4f' => new Room($this->graph, 'Annie',             0x4f, 0x00, 0x00, 0x10),
        //  'Room 50' => new Room($this->graph, 'Unused',            0x50, 0x00, 0x00, 0x0c),
            'Room 51' => new Room($this->graph, 'Connector',         0x51, 0x00, 0x0c, 0x0c),
            'Room 52' => new Room($this->graph, 'Connector',         0x52, 0x69, 0x84, 0x0c),
            'Room 53' => new Room($this->graph, 'Vest',              0x53, 0x0c, 0x40, 0x0c),
            'Room 54' => new Room($this->graph, 'Connector',         0x54, 0x10, 0x0c, 0x0d),
            'Room 55' => new Room($this->graph, 'Connector',         0x55, 0x6b, 0x44, 0x0d),
            'Room 56' => new Room($this->graph, 'Fire Box 4',        0x56, 0x40, 0x80, 0x0d),
            'Room 57' => new Room($this->graph, 'Device 4',          0x57, 0x80, 0x0c, 0x0d),
            'Room 58' => new Room($this->graph, 'Connector',         0x58, 0x0e, 0x04, 0x0d),
            'Room 59' => new Room($this->graph, 'Connector',         0x59, 0x00, 0x84, 0x0c),
            'Room 5a' => new Room($this->graph, 'Old Lady',          0x5a, 0x00, 0x40, 0x0c),
            'Room 5b' => new Room($this->graph, 'Connector',         0x5b, 0x0e, 0x04, 0x0c),
            'Room 5c' => new Room($this->graph, 'Speed Shoes 2',     0x5c, 0x68, 0x04, 0x0c),
            'Room 5d' => new Room($this->graph, 'Connector',         0x5d, 0x6b, 0x44, 0x0d),
            'Room 5e' => new Room($this->graph, 'Connector',         0x5e, 0x00, 0x81, 0x0d),
            'Room 5f' => new Room($this->graph, 'Warp Dude',         0x5f, 0x00, 0x02, 0x0d),
        //  'Room 60' => new Room($this->graph, 'Unused',            0x60, 0x00, 0x0c, 0x0c),
        //  'Room 61' => new Room($this->graph, 'Unused',            0x61, 0x10, 0x0f, 0x0c),
            'Room 62' => new Room($this->graph, 'Empty',             0x62, 0x00, 0x04, 0x0d),
            'Room 63' => new Room($this->graph, 'Connector',         0x63, 0x00, 0x01, 0x0f),
            'Room 64' => new Room($this->graph, 'Goonie 6',          0x64, 0x00, 0x02, 0x0f),
            'Room 65' => new Room($this->graph, 'Connector',         0x65, 0x0f, 0x46, 0x0c),
            'Room 66' => new Room($this->graph, 'Connector',         0x66, 0x10, 0x01, 0x0c),
            'Room 67' => new Room($this->graph, 'Fire Box 3',        0x67, 0x80, 0x80, 0x0c),
            'Room 68' => new Room($this->graph, 'Warp Zone',         0x68, 0x00, 0x0d, 0x0e),
            'Room 69' => new Room($this->graph, 'Alien',             0x69, 0x00, 0x02, 0x0e),
            'Room 6a' => new Room($this->graph, 'Warp Zone',         0x6a, 0x00, 0x0c, 0x0c),
            'Room 6b' => new Room($this->graph, 'Connector',         0x6b, 0x90, 0x41, 0x0c),
            'Room 6c' => new Room($this->graph, 'Warp Zone',         0x6c, 0x00, 0x06, 0x0c),
            'Room 6d' => new Room($this->graph, 'Hint 10',           0x6d, 0x00, 0x04, 0x0c),
            'Room 6e' => new Room($this->graph, 'Connector',         0x6e, 0x7a, 0x04, 0x0c),
            'Room 6f' => new Room($this->graph, 'Connector',         0x6f, 0x00, 0x06, 0x0d),
            'Room 70' => new Room($this->graph, 'Connector',         0x70, 0x7a, 0x01, 0x0d),
            'Room 71' => new Room($this->graph, 'Connector',         0x71, 0x6b, 0x44, 0x0d),
            'Room 72' => new Room($this->graph, 'Connector',         0x72, 0x10, 0x82, 0x0d),
            'Room 73' => new Room($this->graph, 'Connector',         0x73, 0x00, 0x41, 0x0d),
            'Room 74' => new Room($this->graph, 'Old Lady',          0x74, 0x00, 0x80, 0x0d),
            'Room 75' => new Room($this->graph, 'Connector',         0x75, 0x69, 0x84, 0x0e),
            'Room 76' => new Room($this->graph, 'Goonie 5',          0x76, 0x00, 0x40, 0x0e),
            'Room 77' => new Room($this->graph, 'Warp Zone',         0x77, 0x00, 0x04, 0x0d),
            'Room 78' => new Room($this->graph, 'Konamiman',         0x78, 0x00, 0x04, 0x0e),
            'Room 79' => new Room($this->graph, 'Sprint Shoes',      0x79, 0x80, 0x04, 0x0e),
            'Room 7a' => new Room($this->graph, 'Frogman',           0x7a, 0x00, 0x00, 0x0f),
            'Room 7b' => new Room($this->graph, 'Konamiman',         0x7b, 0x00, 0x04, 0x0e),
            'Room 7c' => new Room($this->graph, 'Konamiman',         0x7c, 0x00, 0x04, 0x0e),
            'Room 7d' => new Room($this->graph, 'Old Man Life',      0x7d, 0x00, 0x04, 0x0c),
            'Room 7e' => new Room($this->graph, 'Konamiman',         0x7e, 0x00, 0x04, 0x0c),
        ]);

        // Item Locations
        $this->vertices = array_merge($this->vertices, [
            'Item 00' => new Location\Visible($this->graph, 'Item 00', $this->vertices['Room 00']),
            'Item 01' => new Location\Visible($this->graph, 'Item 01', $this->vertices['Room 01']),
            'Item 03' => new Location\Visible($this->graph, 'Item 03', $this->vertices['Room 03']),
            'Item 05' => new Location\Visible($this->graph, 'Item 05', $this->vertices['Room 05']),
            'Item 14' => new Location\Visible($this->graph, 'Item 14', $this->vertices['Room 14']),
            'Item 1d' => new Location\NPCItem($this->graph,     'Item 1d', $this->vertices['Room 1d'], 0x00),
            'Item 2a' => new Location\Visible($this->graph, 'Item 2a', $this->vertices['Room 2a']),
            'Item 4a' => new Location\NPCItem($this->graph,     'Item 4a', $this->vertices['Room 4a'], 0x01),
            'Item 46' => new Location\Visible($this->graph, 'Item 46', $this->vertices['Room 46']),
            'Item 57' => new Location\Visible($this->graph, 'Item 57', $this->vertices['Room 57']),
            'Item 67' => new Location\Visible($this->graph, 'Item 67', $this->vertices['Room 67']),
            'Item 79' => new Location\Visible($this->graph, 'Item 79', $this->vertices['Room 79']),
            'Item 20' => new Location\Visible($this->graph, 'Item 20', $this->vertices['Room 20']),

            'Item 21' => new Location\Punch($this->graph,    'Item 21', $this->vertices['Room 21']),
            'Item 07' => new Location\Punch($this->graph,    'Item 07', $this->vertices['Room 07']),
            'Item 0d' => new Location\Punch($this->graph,    'Item 0d', $this->vertices['Room 0d']),
            'Item 0e' => new Location\Punch($this->graph,    'Item 0e', $this->vertices['Room 0e']),
            'Item 0f' => new Location\PunchNPC($this->graph, 'Item 0f', $this->vertices['Room 0f']),
            'Item 25' => new Location\Punch($this->graph,    'Item 25', $this->vertices['Room 25']),
            'Item 39' => new Location\Punch($this->graph,    'Item 39', $this->vertices['Room 39']),
            'Item 3c' => new Location\Punch($this->graph,    'Item 3c', $this->vertices['Room 3c']),
            'Item 45' => new Location\Punch($this->graph,    'Item 45', $this->vertices['Room 45']),
            'Item 47' => new Location\Punch($this->graph,    'Item 47', $this->vertices['Room 47']),
            'Item 4b' => new Location\Punch($this->graph,    'Item 4b', $this->vertices['Room 4b']),
            'Item 4c' => new Location\Punch($this->graph,    'Item 4c', $this->vertices['Room 4c']),
            'Item 56' => new Location\Punch($this->graph,    'Item 56', $this->vertices['Room 56']),

            'Hint 1'  => new Location\SafeHint($this->graph, 'Hint 1',  $this->vertices['Room 02'], 0x01),
            'Hint 2'  => new Location\SafeHint($this->graph, 'Hint 2',  $this->vertices['Room 0c'], 0x02),
            'Hint 3'  => new Location\SafeHint($this->graph, 'Hint 3',  $this->vertices['Room 17'], 0x03),
            'Hint 5'  => new Location\SafeHint($this->graph, 'Hint 5',  $this->vertices['Room 33'], 0x05),
            'Hint 4'  => new Location\SafeHint($this->graph, 'Hint 4',  $this->vertices['Room 29'], 0x04),
            'Hint 6'  => new Location\SafeHint($this->graph, 'Hint 6',  $this->vertices['Room 34'], 0x06),
            'Hint 7'  => new Location\SafeHint($this->graph, 'Hint 7',  $this->vertices['Room 3e'], 0x07),
            'Hint 8'  => new Location\SafeHint($this->graph, 'Hint 8',  $this->vertices['Room 44'], 0x08),
            'Hint 9'  => new Location\SafeHint($this->graph, 'Hint 9',  $this->vertices['Room 48'], 0x09),
            'Hint 10' => new Location\SafeHint($this->graph, 'Hint 10', $this->vertices['Room 6d'], 0x0a),
            'Item e9' => new Location\SafeItem($this->graph, 'Item e9', $this->vertices['Room 41'], 0x03),
            'Item e8' => new Location\SafeItem($this->graph, 'Item e8', $this->vertices['Room 53'], 0x02),
            'Item e7' => new Location\SafeItem($this->graph, 'Item e7', $this->vertices['Room 12'], 0x01),
            'Item ea' => new Location\SafeItem($this->graph, 'Item ea', $this->vertices['Room 5c'], 0x04),
            'Item ec' => new Location\SafeItem($this->graph, 'Item ec', $this->vertices['Room 3a'], 0x06),

            'Goonie 1' => new Location\Cage($this->graph, 'Goonie 1', $this->vertices['Room 09']),
            'Goonie 2' => new Location\Cage($this->graph, 'Goonie 2', $this->vertices['Room 19']),
            'Goonie 3' => new Location\Cage($this->graph, 'Goonie 3', $this->vertices['Room 4e']),
            'Goonie 4' => new Location\Cage($this->graph, 'Goonie 4', $this->vertices['Room 36']),
            'Annie'    => new Location\Cage($this->graph, 'Annie',    $this->vertices['Room 4f']),
            'Goonie 6' => new Location\Cage($this->graph, 'Goonie 6', $this->vertices['Room 64']),
            'Goonie 5' => new Location\Cage($this->graph, 'Goonie 5', $this->vertices['Room 76']),
        ]);

        $this->defaultEdges();
        $this->setEdges($items);

        $this->locations = new LocationCollection(array_filter($this->vertices, function ($vertex) {
            return $vertex instanceof Location;
        }));
    }

    /**
     * Get the Graph associated with this world.
     *
     * @return \Fhaculty\Graph\Graph
     */
    public function getGraph() : Graph
    {
        return $this->graph;
    }

    /**
     * Get the Graph associated with this world.
     *
     * @param \Fhaculty\Graph\Vertex $start starting vertex
     *
     * @throws \OutOfBoundsException if the Vertex doesn't exist
     *
     * @return \Fhaculty\Graph\Graph
     */
    public function getReachableGraphFromVertex(Vertex $start) : Graph
    {
        $alg = new BreadthFirst($start);

        return $this->graph->createGraphCloneVertices($alg->getVertices());
    }

    /**
     * Get the Item Locations associated with this world.
     *
     * @return \App\Support\LocationCollection
     */
    public function getLocations() : LocationCollection
    {
        return $this->locations;
    }

    /**
     * Get the Locations that can be collected.
     *
     * @param \Fhaculty\Graph\Vertex $start starting location to collect from
     *
     * @return \App\Support\LocationCollection
     */
    public function getReachableLocationsFrom(Vertex $start) : LocationCollection
    {
        $graph = $this->getReachableGraphFromVertex($start);
        $vertices = array_map(function ($vertex) {
            return $this->vertices[$vertex->getId()];
        }, $graph->getVertices()->getVector());

        return new LocationCollection(array_filter($vertices, function ($vertex) {
            return $vertex instanceof Location;
        }));
    }

    /**
     * Get the empty Item Locations associated with this world.
     *
     * @return \App\Support\LocationCollection
     */
    public function getEmptyLocations() : LocationCollection
    {
        return $this->locations->getEmptyLocations();
    }

    /**
     * Get the Items that can be collected.
     *
     * @param \Fhaculty\Graph\Vertex $start starting location to collect from
     *
     * @return \App\Support\ItemCollection
     */
    public function collectItemsFrom(Vertex $start) : ItemCollection
    {
        $graph = $this->getReachableGraphFromVertex($start);

        $locations = new LocationCollection($graph->getVertices()->getVerticesMatch(function ($vertex) {
            return $vertex instanceof Location;
        })->getVector());

        return $locations->getNonEmptyLocations()->getItems();
    }

    /**
     * Get the Items in this world.
     *
     * @return \App\Support\ItemCollection
     */
    public function getItems() : ItemCollection
    {
        $locations = new LocationCollection($this->graph->getVertices()->getVerticesMatch(function ($vertex) {
            return $vertex instanceof Location;
        })->getVector());

        return $locations->getNonEmptyLocations()->getItems();
    }

    /**
     * Get Vertex by name.
     *
     * @param string $name name of vertex
     *
     * @throws \OutOfBoundsException if the Vertex doesn't exist
     *
     * @return \Fhaculty\Graph\Vertex
     */
    public function getVertex(string $name) : Vertex
    {
        if (!isset($this->vertices[$name])) {
            throw new \OutOfBoundsException;
        }

        return $this->vertices[$name];
    }

    /**
     * Get Location by name.
     *
     * @param string $name name of location
     *
     * @throws \OutOfBoundsException if the Location doesn't exist
     *
     * @return \App\Location
     */
    public function getLocation(string $name) : Location
    {
        if (!isset($this->locations[$name])) {
            throw new \OutOfBoundsException;
        }

        return $this->locations[$name];
    }

    /**
     * Get all rooms that can have a Goonie.
     *
     * @return \App\Support\Collection
     */
    public function getPotentialGoonieRooms() : Collection
    {
        $itemRooms = array_map(function ($location) {
            return $location->getRoom();
        }, $this->locations->filter(function ($location) {
            return !$location instanceof Location\Cage;
        })->all());

        return new Collection($this->graph->getVertices()->getVerticesMatch(function ($vertex) use ($itemRooms) {
            return $vertex instanceof Room
                && $vertex->canHoldGoonie()
                && !in_array($vertex, $itemRooms);
        })->getVector());
    }

    /**
     * Set the items for creating the edges for the world.
     *
     * @param \App\Support\ItemCollection $items Items to generate edges for graph with
     *
     * @return void
     */
    public function setItems(ItemCollection $items) : void
    {
        $edges = $this->graph->getEdges();
        foreach ($edges as $edge) {
            $edge->destroy();
        }

        $this->defaultEdges();
        $this->setEdges($items);
    }

    /**
     * These edges should always be set.
     *
     * @return void
     */
    protected function defaultEdges() : void
    {
        $this->vertices['Start']->createEdge($this->vertices['Front - Orange House Left']);
        $this->vertices['Front - Orange House Left']->createEdge($this->vertices['Front - Gray Basement Left']);
        $this->vertices['Front - Orange House Left']->createEdge($this->vertices['Room 00']);
        $this->vertices['Front - Orange House Left']->createEdge($this->vertices['Room 01']);
        $this->vertices['Room 01']->createEdge($this->vertices['Room 02']);

        $this->vertices['Front - Attic']->createEdge($this->vertices['Room 6c']);
        $this->vertices['Front - Attic']->createEdge($this->vertices['Room 6d']);
        $this->vertices['Front - Attic']->createEdge($this->vertices['Room 59']);

        $this->vertices['Front - Attic']->createEdge($this->vertices['Room 6a']);

        $this->vertices['Back - Gray House Right']->createEdge($this->vertices['Room 6a']);
        $this->vertices['Back - Gray House Right']->createEdge($this->vertices['Room 65']);
        $this->vertices['Front - Orange House Right']->createEdge($this->vertices['Front - Gray Basement Right']);

        $this->vertices['Room 72']->createEdge($this->vertices['Room 73']);

        $this->vertices['Front - Gray Basement Right']->createEdge($this->vertices['Room 6f']);

        $this->vertices['Back - Gray Basement Right']->createEdge($this->vertices['Room 77']);

        $this->vertices['Front - Gray Basement Left']->createEdge($this->vertices['Room 05']);
        $this->vertices['Front - Gray Basement Left']->createEdge($this->vertices['Room 03']);
        $this->vertices['Room 03']->createEdge($this->vertices['Room 04']);
        $this->vertices['Front - Gray Basement Left']->createEdge($this->vertices['Room 06']); // to red caves

        $this->vertices['Back - Gray Basement Left']->createEdge($this->vertices['Back - Gray House Left']);
        $this->vertices['Back - Gray Basement Left']->createEdge($this->vertices['Room 05']);
        $this->vertices['Back - Gray Basement Left']->createEdge($this->vertices['Room 0b']);
        $this->vertices['Room 0b']->createEdge($this->vertices['Room 0c']);
        $this->vertices['Room 0b']->createEdge($this->vertices['Room 0d']);

        $this->vertices['Back - Gray House Left']->createEdge($this->vertices['Room 07']);
        $this->vertices['Room 07']->createEdge($this->vertices['Room 08']);
        $this->vertices['Room 09']->createEdgeTo($this->vertices['Room 08']);

        $this->vertices['Back - Gray House Left']->createEdge($this->vertices['Room 30']);

        $this->vertices['Back - Red Caves']->createEdge($this->vertices['Room 06']); // to F gray basement
        $this->vertices['Back - Red Caves']->createEdge($this->vertices['Room 0e']); // to F orange caves L
        $this->vertices['Room 0e']->createEdge($this->vertices['Room 0f']);
        $this->vertices['Back - Red Caves']->createEdge($this->vertices['Room 78']);
        $this->vertices['Back - Red Caves']->createEdge($this->vertices['Room 1a']); // to Ice L
        $this->vertices['Room 1a']->createEdge($this->vertices['Room 1b']);
        $this->vertices['Back - Red Caves']->createEdge($this->vertices['Room 16']); // to bridge
        $this->vertices['Room 19']->createEdgeTo($this->vertices['Room 18']);

        $this->vertices['Front - Orange Caves Left']->createEdge($this->vertices['Room 0e']);

        $this->vertices['Front - Orange Caves Right']->createEdge($this->vertices['Room 16']); // to red caves
        $this->vertices['Front - Orange Caves Right']->createEdge($this->vertices['Front - Bridge']);

        $this->vertices['Front - Bridge']->createEdge($this->vertices['Front - Purple Caves']);

        $this->vertices['Front - Purple Caves']->createEdge($this->vertices['Room 28']);
        $this->vertices['Front - Purple Caves']->createEdge($this->vertices['Room 0a']);
        $this->vertices['Front - Purple Caves']->createEdge($this->vertices['Room 7c']);

        $this->vertices['Room 22']->createEdgeTo($this->vertices['Front - Purple Caves']);
        $this->vertices['Room 22']->createEdge($this->vertices['Room 23']);
        $this->vertices['Room 23']->createEdge($this->vertices['Room 25']);

        $this->vertices['Room 24']->createEdgeTo($this->vertices['Room 23']);
        $this->vertices['Front - Purple Caves']->createEdge($this->vertices['Room 26']);

        $this->vertices['Room 27']->createEdgeTo($this->vertices['Room 26']);

        $this->vertices['Back - Green Caves']->createEdge($this->vertices['Room 27']);
        $this->vertices['Back - Green Caves']->createEdge($this->vertices['Room 31']);
        $this->vertices['Room 31']->createEdge($this->vertices['Room 32']);
        $this->vertices['Room 32']->createEdge($this->vertices['Room 33']);
        $this->vertices['Room 31']->createEdge($this->vertices['Room 34']);
        $this->vertices['Room 34']->createEdge($this->vertices['Room 35']);
        $this->vertices['Room 32']->createEdge($this->vertices['Room 35']);

        $this->vertices['Room 36']->createEdgeTo($this->vertices['Room 35']);
        $this->vertices['Back - Green Caves']->createEdge($this->vertices['Room 37']);
        $this->vertices['Room 37']->createEdge($this->vertices['Room 38']);
        // We specifically ignore the Room 38 -> Room 38 connection so we can walk the map.

        $this->vertices['Room 39']->createEdgeTo($this->vertices['Room 38']);
        $this->vertices['Back - Green Caves']->createEdge($this->vertices['Room 3a']);
        $this->vertices['Back - Green Caves']->createEdge($this->vertices['Room 42']);
        $this->vertices['Room 42']->createEdge($this->vertices['Room 43']);
        $this->vertices['Room 42']->createEdge($this->vertices['Room 45']);
        $this->vertices['Room 43']->createEdge($this->vertices['Room 44']);
        $this->vertices['Room 44']->createEdge($this->vertices['Room 45']);
        $this->vertices['Back - Green Caves']->createEdge($this->vertices['Room 3d']);
        $this->vertices['Room 3d']->createEdge($this->vertices['Room 3e']);
        $this->vertices['Room 3d']->createEdge($this->vertices['Room 3f']);
        $this->vertices['Room 3e']->createEdge($this->vertices['Room 40']);
        $this->vertices['Room 3f']->createEdge($this->vertices['Room 40']);

        $this->vertices['Room 41']->createEdgeTo($this->vertices['Room 40']);
        $this->vertices['Back - Green Caves']->createEdge($this->vertices['Room 3b']);
        $this->vertices['Room 3b']->createEdge($this->vertices['Room 3c']);

        $this->vertices['Room 12']->createEdge($this->vertices['Room 13']); // to water
        $this->vertices['Room 12']->createEdge($this->vertices['Room 14']);
        $this->vertices['Room 14']->createEdge($this->vertices['Room 15']);

        $this->vertices['Front - Ice Caves Right']->createEdge($this->vertices['Room 1b']);
        $this->vertices['Front - Ice Caves Right']->createEdge($this->vertices['Room 17']);
        $this->vertices['Room 17']->createEdge($this->vertices['Room 18']);

        $this->vertices['Back - Aquarium Left']->createEdge($this->vertices['Room 47']);
        $this->vertices['Room 47']->createEdge($this->vertices['Room 46']);

        $this->vertices['Room 4b']->createEdge($this->vertices['Room 4c']);
        $this->vertices['Room 4b']->createEdge($this->vertices['Room 4d']);

        $this->vertices['Room 49']->createEdge($this->vertices['Room 4a']);

        $this->vertices['Back - Orange Cabin']->createEdge($this->vertices['Room 24']);
        $this->vertices['Back - Orange Cabin']->createEdge($this->vertices['Room 57']);
        $this->vertices['Back - Orange Cabin']->createEdge($this->vertices['Room 68']);
        $this->vertices['Room 68']->createEdge($this->vertices['Room 69']);

        $this->vertices['Front - Lava Pit']->createEdge($this->vertices['Room 68']);
        $this->vertices['Front - Lava Pit']->createEdge($this->vertices['Room 75']);

        $this->vertices['Front - Lava Pit']->createEdge($this->vertices['Room 7b']);
        $this->vertices['Front - Lava Pit']->createEdge($this->vertices['Room 2b']);
        $this->vertices['Room 2b']->createEdge($this->vertices['Room 2c']);
        $this->vertices['Room 2c']->createEdge($this->vertices['Room 2d']);
        $this->vertices['Room 2d']->createEdge($this->vertices['Room 2e']);

        $this->vertices['Room 2f']->createEdge($this->vertices['Room 30']);

        $this->vertices['Front - Brown Castle']->createEdge($this->vertices['Room 57']);
        $this->vertices['Front - Brown Castle']->createEdge($this->vertices['Front - Green Cabin']);
        $this->vertices['Front - Brown Castle']->createEdge($this->vertices['Room 58']);
        $this->vertices['Front - Brown Castle']->createEdge($this->vertices['Room 5b']);

        $this->vertices['Room 5c']->createEdgeTo($this->vertices['Room 5b']);

        $this->vertices['Back - Red Castle']->createEdgeTo($this->vertices['Room 58']);

        $this->vertices['Back - Red Castle']->createEdge($this->vertices['Room 7e']);
        $this->vertices['Back - Red Castle']->createEdge($this->vertices['Room 62']);
        $this->vertices['Front - Green Cabin']->createEdge($this->vertices['Room 5d']);
        $this->vertices['Room 5e']->createEdge($this->vertices['Room 5f']);
        $this->vertices['Front - Green Cabin']->createEdge($this->vertices['Room 51']);
        $this->vertices['Room 51']->createEdge($this->vertices['Room 52']);
        $this->vertices['Room 63']->createEdge($this->vertices['Room 64']);

        $this->vertices['Item 00']->createEdgeToRoom();
        $this->vertices['Item 01']->createEdgeToRoom();
        $this->vertices['Item 03']->createEdgeToRoom();
        $this->vertices['Item 05']->createEdgeToRoom();
        $this->vertices['Item 14']->createEdgeToRoom();
        $this->vertices['Item 1d']->createEdgeToRoom();
        $this->vertices['Item 2a']->createEdgeToRoom();
        $this->vertices['Item 46']->createEdgeToRoom();
        $this->vertices['Item 4a']->createEdgeToRoom();
        $this->vertices['Item 57']->createEdgeToRoom();
        $this->vertices['Item 67']->createEdgeToRoom();
        $this->vertices['Item 79']->createEdgeToRoom();
        $this->vertices['Item 20']->createEdgeToRoom();
        $this->vertices['Item 21']->createEdgeToRoom();
        $this->vertices['Item 07']->createEdgeToRoom();
        $this->vertices['Item 0d']->createEdgeToRoom();
        $this->vertices['Item 0e']->createEdgeToRoom();
        $this->vertices['Item 0f']->createEdgeToRoom();
        $this->vertices['Item 25']->createEdgeToRoom();
        $this->vertices['Item 39']->createEdgeToRoom();
        $this->vertices['Item 3c']->createEdgeToRoom();
        $this->vertices['Item 45']->createEdgeToRoom();
        $this->vertices['Item 47']->createEdgeToRoom();
        $this->vertices['Item 4b']->createEdgeToRoom();
        $this->vertices['Item 4c']->createEdgeToRoom();
        $this->vertices['Item 56']->createEdgeToRoom();
    }

    /**
     * Set conditional edges based on Items.
     *
     * @param \App\Support\ItemCollection $items items to determine which edges to enable
     *
     * @return void
     */
    protected function setEdges(ItemCollection $items) : void
    {
        if ($items->hasKeys()) {
            $this->vertices['Hint 1']->createEdgeToRoom();
            $this->vertices['Hint 2']->createEdgeToRoom();
            $this->vertices['Hint 3']->createEdgeToRoom();
            $this->vertices['Hint 5']->createEdgeToRoom();
            $this->vertices['Hint 4']->createEdgeToRoom();
            $this->vertices['Hint 6']->createEdgeToRoom();
            $this->vertices['Hint 7']->createEdgeToRoom();
            $this->vertices['Hint 8']->createEdgeToRoom();
            $this->vertices['Hint 9']->createEdgeToRoom();
            $this->vertices['Hint 10']->createEdgeToRoom();

            $this->vertices['Goonie 1']->createEdgeToRoom();
            $this->vertices['Goonie 2']->createEdgeToRoom();
            $this->vertices['Goonie 3']->createEdgeToRoom();
            $this->vertices['Goonie 4']->createEdgeToRoom();
            $this->vertices['Annie']->createEdgeToRoom();
            $this->vertices['Goonie 6']->createEdgeToRoom();
            $this->vertices['Goonie 5']->createEdgeToRoom();

            if ($items->has('Glasses')) {
                $this->vertices['Item e9']->createEdgeToRoom();
                $this->vertices['Item e8']->createEdgeToRoom();
            }
            if ($items->has('Hammer')) {
                $this->vertices['Item e7']->createEdgeToRoom();
                $this->vertices['Item ea']->createEdgeToRoom();
                $this->vertices['Item ec']->createEdgeToRoom();
            }
        }

        if ($items->hasBombs()) {
            $this->vertices['Front - Green Cabin']->createEdge($this->vertices['Room 7d']);
            $this->vertices['Back - Aquarium Left']->createEdge($this->vertices['Room 7a']);
            $this->vertices['Front - Bridge']->createEdge($this->vertices['Room 79']);
        }

        if ($items->has('Hammer')) {
            $this->vertices['Room 18']->createEdgeTo($this->vertices['Room 19']);
            $this->vertices['Room 40']->createEdgeTo($this->vertices['Room 41']);
            $this->vertices['Room 38']->createEdgeTo($this->vertices['Room 39']);
            $this->vertices['Room 26']->createEdgeTo($this->vertices['Room 27']);
            $this->vertices['Room 08']->createEdgeTo($this->vertices['Room 09']);

            if ($items->has('Ladder')) {
                $this->vertices['Room 52']->createEdgeTo($this->vertices['Room 53']);
                $this->vertices['Room 5d']->createEdgeTo($this->vertices['Room 5e']);
                $this->vertices['Room 55']->createEdgeTo($this->vertices['Room 56']);
                $this->vertices['Room 75']->createEdgeTo($this->vertices['Room 76']);
                $this->vertices['Room 2c']->createEdge($this->vertices['Room 2f']);
                $this->vertices['Room 4d']->createEdge($this->vertices['Room 4e']);
            }
        }

        if ($items->has('Glasses')) {
            $this->vertices['Room 58']->createEdgeTo($this->vertices['Back - Red Castle']);
            $this->vertices['Room 5b']->createEdgeTo($this->vertices['Room 5c']);
            $this->vertices['Room 35']->createEdgeTo($this->vertices['Room 36']);
            $this->vertices['Room 23']->createEdgeTo($this->vertices['Room 24']);

            if ($items->has('Ladder')) {
                $this->vertices['Room 65']->createEdge($this->vertices['Room 67']);
            }
        }

        if ($items->has('Ladder')) {
            $this->vertices['Room 53']->createEdgeTo($this->vertices['Room 52']);
            $this->vertices['Room 5e']->createEdgeTo($this->vertices['Room 5d']);
            $this->vertices['Room 56']->createEdgeTo($this->vertices['Room 55']);
            $this->vertices['Room 76']->createEdgeTo($this->vertices['Room 75']);
            $this->vertices['Room 59']->createEdge($this->vertices['Room 5a']);
            $this->vertices['Room 73']->createEdge($this->vertices['Room 74']);
        }

        if ($items->has('DivingSuit')) {
            $this->vertices['Back - Aquarium Right']->createEdge($this->vertices['Room 5f']);
            $this->vertices['Back - Aquarium Right']->createEdge($this->vertices['Room 63']);
            $this->vertices['Front - Aquarium Left']->createEdge($this->vertices['Room 46']);
            $this->vertices['Front - Aquarium Left']->createEdge($this->vertices['Room 49']);
            $this->vertices['Front - Aquarium Left']->createEdge($this->vertices['Room 48']);
            $this->vertices['Back - Aquarium Left']->createEdge($this->vertices['Room 4b']);
            $this->vertices['Back - Aquarium Left']->createEdge($this->vertices['Room 13']);
            $this->vertices['Front - Aquarium Right']->createEdge($this->vertices['Room 77']);
            $this->vertices['Front - Aquarium Right']->createEdge($this->vertices['Room 4f']);
        }

        if ($items->has('JumpShoes')) {
            $this->vertices['Front - Purple Caves']->createEdgeTo($this->vertices['Room 22']);
        }

        if ($items->has('Candle')) {
            $this->vertices['Room 6b']->createEdge($this->vertices['Room 6c']);
            $this->vertices['Room 65']->createEdge($this->vertices['Room 66']);

            $this->vertices['Back - Gray House Right']->createEdgeTo($this->vertices['Room 6e']);

            $this->vertices['Front - Orange House Right']->createEdge($this->vertices['Room 6e']);

            $this->vertices['Front - Gray Basement Right']->createEdge($this->vertices['Room 71']);

            $this->vertices['Room 6f']->createEdge($this->vertices['Room 70']);

            $this->vertices['Back - Gray Basement Right']->createEdgeTo($this->vertices['Room 70']);

            $this->vertices['Back - Red Caves']->createEdge($this->vertices['Room 1c']);

            $this->vertices['Back - Red Caves']->createEdge($this->vertices['Room 10']); // to Ice L

            $this->vertices['Front - Orange Caves Left']->createEdge($this->vertices['Room 1e']);
            $this->vertices['Room 1e']->createEdge($this->vertices['Room 1f']);
            $this->vertices['Room 1e']->createEdge($this->vertices['Room 20']);
            $this->vertices['Room 1f']->createEdge($this->vertices['Room 21']);
            $this->vertices['Room 20']->createEdge($this->vertices['Room 21']);

            $this->vertices['Room 28']->createEdge($this->vertices['Room 29']);
            $this->vertices['Room 29']->createEdge($this->vertices['Room 2a']);

            $this->vertices['Front - Ice Caves Left']->createEdge($this->vertices['Room 10']);
            $this->vertices['Front - Ice Caves Left']->createEdge($this->vertices['Room 11']);
            $this->vertices['Room 11']->createEdge($this->vertices['Room 12']);

            $this->vertices['Back - Red Castle']->createEdge($this->vertices['Room 54']);
            $this->vertices['Room 54']->createEdge($this->vertices['Room 55']);

            if ($items->has('Hammer')) {
                $this->vertices['Room 70']->createEdgeTo($this->vertices['Back - Gray Basement Right']);
                $this->vertices['Room 6e']->createEdgeTo($this->vertices['Back - Gray House Right']);

                if ($items->has('Ladder')) {
                    $this->vertices['Room 71']->createEdgeTo($this->vertices['Room 72']);
                }
            }

            if ($items->has('Glasses')) {
                $this->vertices['Room 1c']->createEdge($this->vertices['Room 1d']);
            }

            if ($items->has('Ladder')) {
                $this->vertices['Room 72']->createEdgeTo($this->vertices['Room 71']);
                $this->vertices['Room 02']->createEdge($this->vertices['Room 6b']);
            }
        }
    }
}
