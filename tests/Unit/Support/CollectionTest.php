<?php

namespace Tests\Unit\Support;

use App\Support\Collection;
use Tests\TestCase;

class CollectionTest extends TestCase
{
    /**
     * It's highly unlikely that 2048 elements will shuffle to the same order.
     *
     * @return void
     */
    public function testShuffle()
    {
        $collection = new Collection(range(0, 2047));
        $data = array_values($collection->all());
        $shuffled = array_values($collection->shuffle()->all());

        $this->assertNotEquals($data, $shuffled);

        sort($data);
        sort($shuffled);
        $this->assertEquals($data, $shuffled);
    }
}
