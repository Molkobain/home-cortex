<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Controller;

use Exception;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Molkobain\HomeCortex\Helper\Transport\MetromobiliteAPIHelper;

/**
 * Class TransportController
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class TransportController extends AbstractController {

    public function showFavoritesAction(Request $oRequest, Application $oApp) {
        $aData = [];

        // Retrieving stops from config
        $aRequestedStops = [];
        try
        {
            $aRequestedStops = $oApp['parameters']['transport_providers']['metromobilite']['favorites']['stops'];
        }
        catch(Exception $e)
        {
            // Do nothing
        }

        // - Sorting stops by rank
        uasort($aRequestedStops, function($a, $b) {
                    return $a['rank'] > $b['rank'];
        });
        
        // Parsing stop and route ids
        $aStopIds = [];
        $aRouteIds = [];
        foreach ($aRequestedStops as $sTmpStopId => $aTmpStopData) {
            // Retrieving stop id
            if (!in_array($sTmpStopId, $aStopIds)) {
                $aStopIds[] = $sTmpStopId;
            }
            // Retrieving route id
            foreach ($aTmpStopData['routes'] as $sTmpRouteId) {
                if (!in_array($sTmpRouteId, $aRouteIds)) {
                    $aRouteIds[] = $sTmpRouteId;
                }
            }
        }

        // Retrieving route data
        $aRoutes = MetromobiliteAPIHelper::getRoutes(implode(',', $aRouteIds));

        // Retrieving stop data
        $aStopTimes = [];
        foreach ($aRequestedStops as $sStopId => $aRouteIds) {
            // Retrieving data
            $aTimes = MetromobiliteAPIHelper::getStopTimes($sStopId);
            // Crossing with route data
            foreach ($aTimes as $sTmpRouteIndex => $aTmpStopData) {
                // For each route of that stop we check if it is belong to a requested stop/route
                foreach ($aRequestedStops[$sStopId]['routes'] as $sTmpRouteId) {
                    // Testing if route index STARTS (double equals is important) with the route id
                    if (strpos($sTmpRouteIndex, $sTmpRouteId) === 0) {
                        // Preparing stop/route base data
                        $aTmpStopTime = [
                            'stop' => [
                                'id' => $sStopId,
                                'routeId' => $sTmpRouteId,
                                'name' => $aTmpStopData['name'],
                            ],
                            'route' => $aRoutes[$sTmpRouteId],
                            'direction' => $aTmpStopData['description'],
                            'departures' => []
                        ];
                        // Preparing stop/route times
                        foreach ($aTmpStopData['times'] as $aTmpTime) {
                            $aTmpStopTime['departures'][] = $aTmpTime['departure'];
                        }
                        // Adding it to the final array
                        $aStopTimes[] = $aTmpStopTime;
                    }
                }
            }
        }

        // Preparing response data
        $aData['aStopTimes'] = $aStopTimes;
        $sTemplate = 'transport/mode_favorites.html.twig';

        return $oApp['twig']->render($sTemplate, $aData);
    }

}

?>