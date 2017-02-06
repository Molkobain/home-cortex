<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ClockController
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class ClockController extends AbstractController {

    public function showAction(Request $oRequest, Application $oApp) {
        // Note : This could be passed with JS format so the client knows how to handle it
        $aData = [
            'datetime' => [
                'date' => ucfirst(utf8_encode(strftime('%A %#d %B'))),
                'time' => strftime('%H:%M')
            ]
        ];

        // Preparing response data
        $sTemplate = 'clock/widget.html.twig';
        return $oApp['twig']->render($sTemplate, $aData);
    }

}

?>