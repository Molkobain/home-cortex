<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Router;

/**
 * Class TransportRouter
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class TransportRouter extends AbstractRouter {

    static $aRoutes = [
        ['pattern' => '/transport/favorites',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\TransportController::showFavoritesAction',
            'bind' => 'm_transport_favorites'
        ],
        ['pattern' => '/transport/nearby',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\TransportController::showNearbyAction',
            'bind' => 'm_transport_nearby'
        ],
        ['pattern' => '/transport/search',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\TransportController::searchAction',
            'bind' => 'm_transport_search'
        ],
    ];

}
