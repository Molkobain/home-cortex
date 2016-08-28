<?php

// Copyright (C) 2016 Guillaume Lajarige
//
// lajarige.guillaume@free.fr
// https://github.com/Molkobain

namespace Molkobain\HomeCortex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Molkobain\HomeCortex\Controller\AbstractController;

class DefaultController extends AbstractController {

    public function homeAction(Request $oRequest, Application $oApp) {
        $aData = array();

        // Home page template
        $template = 'layout.html.twig';

        return $oApp['twig']->render($template, $aData);
    }

}

?>