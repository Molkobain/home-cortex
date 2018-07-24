<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Router;

/**
 * Class MusicRouter
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class MusicRouter extends AbstractRouter {

    static $aRoutes = [
        ['pattern' => '/music/channel',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\MusicController::showChannelAction',
            'bind' => 'm_music_channel'
        ],
        ['pattern' => '/music/player',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\MusicController::showPlayerAction',
            'bind' => 'm_music_player_init'
        ],
    ];

}
