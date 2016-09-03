<?php

// Copyright (C) 2016 Guillaume Lajarige
//
// lajarige.guillaume@free.fr
// https://github.com/Molkobain

namespace Molkobain\HomeCortex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Molkobain\HomeCortex\Controller\AbstractController;
use Molkobain\HomeCortex\Helper\Transport\MetromobiliteAPIHelper;

class ClockController extends AbstractController {

    public function showAction(Request $oRequest, Application $oApp) {
        // Note : This could be passed with JS format so the client knows how to handle it
        $aData = array(
            'datetime' => array(
                'date' => ucfirst(strftime('%A %#d %B')),
                'time' => strftime('%H:%M')
            )
        );

        // Preparing response data
        $sTemplate = 'clock/widget.html.twig';

        return $oApp['twig']->render($sTemplate, $aData);
    }

}

?>