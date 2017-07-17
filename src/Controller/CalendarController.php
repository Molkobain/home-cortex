<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Controller;

use Exception;
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

        // Retrieving calendar ids from config
        $aCalendarIds = [];
        try
        {
            $aGoogleCalendarConf = $oApp['parameters']['calendar_providers']['google'];
            // - Primary calendar
            if(isset($aGoogleCalendarConf['primary_id']))
            {
                $aCalendarIds[] = $aGoogleCalendarConf['primary_id'];
            }
            // - Secondary calendars
            if(isset($aGoogleCalendarConf['other_ids']) && is_array($aGoogleCalendarConf['other_ids']))
            {
                $aCalendarIds = array_merge($aCalendarIds, $aGoogleCalendarConf['other_ids']);
            }
        }
        catch(Exception $e)
        {
            // Do nothing
        }

        // Loading next events
        $aResult = GoogleCalendarAPIHelper::getNextEvents($aCalendarIds);

        // Preparing response data
        $aData['events'] = $aResult;
        $sTemplate = 'calendar/widget.html.twig';

        return $oApp['twig']->render($sTemplate, $aData);
    }

}

?>