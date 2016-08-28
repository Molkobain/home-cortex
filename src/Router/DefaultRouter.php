<?php

// Copyright (C) 2016 Guillaume Lajarige
//
// lajarige.guillaume@free.fr
// https://github.com/Molkobain

namespace Molkobain\HomeCortex\Router;

use Molkobain\HomeCortex\Router\AbstractRouter;

class DefaultRouter extends AbstractRouter {

    static $aRoutes = array(
        array('pattern' => '/',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\DefaultController:homeAction',
            'bind' => 'p_home'
        ),
//		// Example route
//		array('pattern' => '/url-pattern',
//			'hash' => 'string-to-be-append-to-the-pattern-after-a-#',
//			'navigation_menu_attr' => array('id' => 'link_id', 'rel' => 'foo'),
//			'callback' => 'Combodo\\iTop\\Portal\\Controller\\DefaultController::exampleAction',
//			'bind' => 'p_example')
    );

}

?>