<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Controller;

use Molkobain\HomeCortex\Helper\Transport\MetromobiliteAPIHelper;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class TransportController
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class TransportController extends AbstractController {

    public function showFavoritesAction(Request $oRequest, Application $oApp) {
        $aData = [];

        $aRequestedStops = [
            'SEM:0750' => [
                'rank' => 1,
                'routes' => ['SEM:C3']
            ],
            'SEM:0749' => [
                'rank' => 1.5,
                'routes' => ['SEM:C3']
            ],
//            'SEM:0235' => [
//                'rank' => 3,
//                'routes' => ['SEM:C3']
//            ],
//            'SEM:0754' => [
//                'rank' => 2,
//                'routes' => ['SEM:C4']
//            ],
        ];
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