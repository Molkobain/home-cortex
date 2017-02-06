<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Router;

/**
 * Class ClockRouter
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class ClockRouter extends AbstractRouter {

    static $aRoutes = [
        ['pattern' => '/clock/show',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\ClockController::showAction',
            'bind' => 'clock_show'
        ],
    ];

}

?>
