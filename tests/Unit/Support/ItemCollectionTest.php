<?php

namespace Tests\Unit\Support;

use Tests\TestCase;
use App\Support\ItemCollection;

class ItemCollectionTest extends TestCase
{
    /**
     * Test creating with non-Item throws exception.
     *
     * @return void
     */
    public function testWrongTypeException()
    {
        $this->expectException(\Exception::class);

        $item = new ItemCollection(['hello']);
    }
}
