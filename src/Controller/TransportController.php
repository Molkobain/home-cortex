<?php

// Copyright (C) 2016 Guillaume Lajarige
//
// lajarige.guillaume@free.fr
// https://github.com/Molkobain

namespace Molkobain\HomeCortex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Molkobain\HomeCortex\Controller\AbstractController;

class TransportController extends AbstractController {

    public function showFavoritesAction(Request $oRequest, Application $oApp) {
        $aData = array();

        // Home page template
        $template = 'transport/mode_favorites.html.twig';

        return $oApp['twig']->render($template, $aData);
    }

}

?>