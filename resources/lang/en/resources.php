<?php
return [
    'header' => 'Resources',
    'cards' => [
        'external' => [
            'header' => 'External Resources',
            'content' => [
                '<ul>'
                    . '<li><a href="https://strategywiki.org/wiki/The_Goonies_II" target="_blank" rel="noopener noreferrer">Vanilla walkthrough of the base game.</a> (this is a good first read)</li>'
                    . '<li><a href="http://www.aerifal.cx/~dermot/mapgoonies2.html" target="_blank" rel="noopener noreferrer">Maps of the Overworld</a></li>'
                . '</ul>',
            ],
        ],
        'changes' => [
            'header' => 'Differences',
            'sections' => [
                [
                    'header' => 'What has been randomized?',
                    'content' => [
                        '<ul>'
                            . '<li>Nearly all unique item locations</li>'
                            . '<li>Goonie Locations</li>'
                        . '</ul>',
                    ],
                ],
                [
                    'header' => 'What has stayed the same?',
                    'content' => [
                        '<ul>'
                            . '<li>NPC’s are currently in their normal locations, this includes the old lady you punch for Candle</li>'
                            . '<li>All doors lead to their rooms that one would expect</li>'
                        . '</ul>',
                    ],
                ],
                [
                    'header' => 'What changed from the original game?',
                    'content' => [
                        'There are a few changes from the original game which enhance gameplay and prevent you from getting stuck.',
                        '<ul>'
                            . '<li>You can press select to pause the game, and if you are in the Overworld you can press UP+A to get teleported back to the starting location of the game.</li>'
                            . '<li>The old lady you punch for a Candle only requires 1 punch to get her to reveal her item.</li>'
                        . '</ul>',
                    ],
                ],
            ],

        ],
    ],
];
