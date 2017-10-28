<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Controller;

use Exception;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class MusicController
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class MusicController extends AbstractController {

    public function showChannelAction(Request $oRequest, Application $oApp) {
        $aData = [];

        // Preparing response data
        $sTemplate = 'music/channel.html.twig';

        return $oApp['twig']->render($sTemplate, $aData);
    }

    public function showPlayerAction(Request $oRequest, Application $oApp) {
        $aData = [];

        // Preparing response data
        $aData['aPlayer']['sAppId'] = $oApp['parameters']['music_providers']['deezer']['app_id'];
        $sTemplate = 'music/widget.html.twig';

        return $oApp['twig']->render($sTemplate, $aData);
    }

}

?>