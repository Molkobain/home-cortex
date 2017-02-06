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
            'bind' => 'transport_favorites'
        ],
        ['pattern' => '/transport/nearby',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\TransportController::showNearbyAction',
            'bind' => 'transport_nearby'
        ],
        ['pattern' => '/transport/search',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\TransportController::searchAction',
            'bind' => 'transport_search'
        ]
//        // We don't set asserts for sBrowseMode on that route, as it the generic one, it can be extended by another brick.
//        array('pattern' => '/browse/{sBrickId}',
//            'callback' => 'Combodo\\iTop\\Portal\\Controller\\BrowseBrickController::DisplayAction',
//            'bind' => 'p_browse_brick'
//        )
    ];

}

?>
