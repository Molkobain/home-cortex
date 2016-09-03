<?php

// Copyright (C) 2016 Guillaume Lajarige
//
// lajarige.guillaume@free.fr
// https://github.com/Molkobain

namespace Molkobain\HomeCortex\Router;

use Molkobain\HomeCortex\Router\AbstractRouter;

class ClockRouter extends AbstractRouter {

    static $aRoutes = array(
        array('pattern' => '/clock/show',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\ClockController::showAction',
            'bind' => 'clock_show'
        ),
    );

}

?>
