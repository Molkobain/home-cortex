<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Controller;

use Molkobain\HomeCortex\Helper\Calendar\GoogleCalendarAPIHelper;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CalendarController
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class CalendarController extends AbstractController {

    public function showAction(Request $oRequest, Application $oApp) {
        // Note : This could be passed with JS format so the client knows how to handle it
        $aData = [];

        $aResult = GoogleCalendarAPIHelper::getNextEvents( ["guillaume.lajarige@gmail.com", "kutfdj0o1j6o4b2f9jh55u369g@group.calendar.google.com", "#contacts@group.v.calendar.google.com"] );

        // Preparing response data
        $aData['events'] = $aResult;
        $sTemplate = 'calendar/widget.html.twig';

        return $oApp['twig']->render($sTemplate, $aData);
    }

}

?>