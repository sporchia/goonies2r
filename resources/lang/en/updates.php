<?php

return [
    'header' => 'Updates',
    'cards' => [
        'v3' => [
            'header' => 'v3',
            'content' => [
                '<ul>'
                    . '<li>Item drops from enemies will always drop a key or bomb first if you have none.</li>'
                    . '<li>Fixed a bug with NPC’s with an item would give multiple items.</li>'
                    . '<li>Fixed bug where Goonies in their vanilla locations were hidden.</li>'
                    . '<li>Added missing item location.</li>'
                    . '</ul>',
            ],
        ],
        'v2' => [
            'header' => 'v2',
            'content' => [
                '<ul>'
                    . '<li>Magic Locator Devices should show on map to the proper Goonie.</li>'
                    . '<li>Graphics have been added to show if you need to Hammer or Glasses in a room.</li>'
                    . '</ul>',
            ],
        ],
        'v1' => [
            'header' => 'v1',
            'content' => [
                '<ul>'
                    . '<li>Goonies can be in any non item room that doesn’t have a forward door.</li>'
                    . '<li>Items randomized among item locations.</li>'
                    . '<li>Graph based randomization.</li>'
                    . '<li>Permalinks to generated roms.</li>'
                    . '<li>Utilize BPS patching format for smaller complete patches.</li>'
                    . '<li>Your copy of ROM is stored in local storage.</li>'
                    . '<li>Website created.</li>'
                    . '<li>Logo courtesy of fmp.</li>'
                    . '<li>Updated start screen.</li>'
                    . '<li>You can pause the game with Select and in the Overworld press UP+A to be warped to the starting area.</li>'
                    . '<li>You only need to punch the old lady once to get her item.</li>'
                    . '</ul>',
            ],
        ],
    ],
];
