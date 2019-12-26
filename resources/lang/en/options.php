<?php

return [
    'header' => 'Options',
    'subheader' => 'The ways to play Goonies 2: Randomizer',
    'cards' => [
        'options' => [
            'header' => 'Current Options',
            'content' => [
                'goonies' => [
                    'header' => __('randomizer.options.goonies.title'),
                    'content' => [
                        __('randomizer.options.goonies.description'),
                    ],
                ],
                'annie' => [
                    'header' => __('randomizer.options.annie.title'),
                    'content' => [
                        __('randomizer.options.annie.description'),
                    ],
                ],
                'items' => [
                    'header' => __('randomizer.options.items.title'),
                    'content' => [
                        __('randomizer.options.items.description'),
                    ],
                ],
            ],
        ],
        'item_pool' => 'Item Pool',
    ],
];
