<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class DefaultController
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class DefaultController extends AbstractController {

    public function homeAction(Request $oRequest, Application $oApp) {
        $aData = [];

        // Home page template
        $sTemplate = 'layout.html.twig';

        return $oApp['twig']->render($sTemplate, $aData);
    }

}

?>