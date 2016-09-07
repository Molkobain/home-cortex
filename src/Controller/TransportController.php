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

class TransportController extends AbstractController {

    public function showFavoritesAction(Request $oRequest, Application $oApp) {
        $aData = array();

        $aRequestedStops = array(
            'SEM:0750' => array(
                'rank' => 1,
                'routes' => array('SEM:C3')
            ),
            'SEM:0749' => array(
                'rank' => 1.5,
                'routes' => array('SEM:C3')
            ),
            'SEM:0754' => array(
                'rank' => 2,
                'routes' => array('SEM:C4')
            )
        );
        // - Sorting stops by rank
        uasort($aRequestedStops, function($a, $b) {
                    return $a['rank'] > $b['rank'];
        });
        
        // Parsing stop and route ids
        $aStopIds = array();
        $aRouteIds = array();
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
        $aStopTimes = array();
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
                        $aTmpStopTime = array(
                            'stop' => array(
                                'id' => $sStopId,
                                'routeId' => $sTmpRouteId
                            ),
                            'route' => $aRoutes[$sTmpRouteId],
                            'direction' => $aTmpStopData['description'],
                            'departures' => array()
                        );
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