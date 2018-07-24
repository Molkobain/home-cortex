<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Router;

/**
 * Class DefaultRouter
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class DefaultRouter extends AbstractRouter {

    static $aRoutes = [
        ['pattern' => '/',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\DefaultController::homeAction',
            'bind' => 'p_home'
        ],
//		// Example route
//		array('pattern' => '/url-pattern',
//			'hash' => 'string-to-be-append-to-the-pattern-after-a-#',
//			'navigation_menu_attr' => array('id' => 'link_id', 'rel' => 'foo'),
//			'callback' => 'Combodo\\iTop\\Portal\\Controller\\DefaultController::exampleAction',
//			'bind' => 'p_example')
    ];

}
