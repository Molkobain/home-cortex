<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Router;

/**
 * Class CalendarRouter
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class CalendarRouter extends AbstractRouter {

    static $aRoutes = [
        ['pattern' => '/calendar/next-events',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\CalendarController::showAction',
            'bind' => 'm_calendar_next_events'
        ],
    ];

}

?>
