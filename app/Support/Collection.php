<?php

namespace App\Support;

use Illuminate\Support\Collection as BaseCollection;

/**
 * Override normal collection to have a better shuffle algorithm.
 */
class Collection extends BaseCollection
{
    /**
     * {@inheritdoc}
     */
    public function shuffle($seed = null)
    {
        $new_array = array_values($this->items);
        $count = count($this->items);

        for ($i = $count - 1; $i >= 0; --$i) {
            $r = random_int(0, $i);
            [$new_array[$i], $new_array[$r]] = [$new_array[$r], $new_array[$i]];
        }

        return new static($new_array);
    }
}
