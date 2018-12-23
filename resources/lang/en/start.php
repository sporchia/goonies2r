<?php

return [
    'header' => 'Start Your Adventure!',
    'subheader' => 'Want to test your skills in a shuffled Fratelli House? You’ve come to the right place!',
    'cards' => [
        'rom' => [
            'header' => '1. Get the ROM',
            'content' => [
                'You’ll need the base ROM. This should be a <span class="font-weight-bold">The Goonies 2</span> ROM.',
                'crc32: aa9ca482<br>md5: d38325cffb9ba2e6f57897c0e9564cc0<br>sha1: 0a5b8fd5e46f56203f8bf12e888f4f2ea1616aa8',
            ],
        ],
        'randomize' => [
            'header' => '2. Choose Your Game Options',
            'content' => [
                'Head on over to <a href="/en/randomizer" target="_blank" rel="noopener noreferrer">' . __('navigation.randomizer') . '</a> and provide your ROM. The next screen will show a variety of game options.',
            ],
        ],
        'emulator' => [
            'header' => '3. Get a Way to Play',
            'content' => [
                'First, you’ll need something to run your newly minted game on. We recommend using an emulator. An emulator is a program that closely replicates NES hardware, allowing you to run NES games on your computer. You can get the recommended emulator, FCEUX, at their website <a href="http://www.fceux.com/" target="_blank" rel="noopener noreferrer">here</a>.',
                'There are other supported ways to play, including on original NES hardware.',
            ],
        ],
        'play' => [
            'header' => '4. Get Playing!',
            'content' => [
                'You’re finally ready to go! The best way to learn is to load up your new ROM and start playing.',
                '<ul>'
                    . '<li>You can pause the game with Select and in the Overworld press UP+A to be warped to the starting area.</li>'
                    . '<li>You only need to punch the old lady once to get her item.</li>'
                . '</ul>',
            ],
        ],
    ],
];
