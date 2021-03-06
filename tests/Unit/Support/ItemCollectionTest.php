<?php

namespace Tests\Unit\Support;

use App\Support\ItemCollection;
use Tests\TestCase;

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
