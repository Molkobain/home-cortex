<?php

// Copyright (C) 2016 Guillaume Lajarige
//
// lajarige.guillaume@free.fr
// https://github.com/Molkobain

namespace Molkobain\HomeCortex\Router;

use Molkobain\HomeCortex\Router\AbstractRouter;

class TransportRouter extends AbstractRouter {

    static $aRoutes = array(
        array('pattern' => '/transport/favorites',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\TransportController::showFavoritesAction',
            'bind' => 'transport_favorites'
        ),
        array('pattern' => '/transport/nearby',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\TransportController::showNearbyAction',
            'bind' => 'transport_nearby'
        ),
        array('pattern' => '/transport/search',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\TransportController::searchAction',
            'bind' => 'transport_search'
        )
//        // We don't set asserts for sBrowseMode on that route, as it the generic one, it can be extended by another brick.
//        array('pattern' => '/browse/{sBrickId}',
//            'callback' => 'Combodo\\iTop\\Portal\\Controller\\BrowseBrickController::DisplayAction',
//            'bind' => 'p_browse_brick'
//        )
    );

}

?>
