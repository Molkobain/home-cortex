<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Helper\Transport;

use Exception;
use DateTime;
use Molkobain\HomeCortex\Helper\DatetimeHelper;
use Molkobain\HomeCortex\Helper\StringHelper;

/**
 * MetromibiliteAPIHelper is based on http://www.metromobilite.fr APIs
 * It allows to retrieve informations on transportation in the Grenoble, FR area
 *
 * More informations on http://www.metromobilite.fr/pages/opendata/OpenDataApi.html
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class MetromobiliteAPIHelper {

    public static $sBaseUrl = 'http://data.metromobilite.fr/';
    public static $aUrls = [
        'routes' => 'api/routers/default/index/routes?codes={sRouteIds}',
        'clustertimes' => 'api/routers/default/index/clusters/{sClusterId}/stoptimes',
        'stoptimes' => 'api/routers/default/index/stops/{sStopId}/stoptimes'
    ];

    /**
     * Returns an array of routes
     *
     * @param mixed $routeIds Route ids can be either an array (eg. array('SEM:C3', 'SEM:C4')) or a string (eg. 'SEM:C4')
     * @return array
     */
    public static function getRoutes($routeIds) {
        // Preparing query
        if (!is_array($routeIds)) {
            $routeIds = [$routeIds];
        }
        $sRouteIds = implode(',', $routeIds);

        // Retrieving data
        $sUrl = str_replace('{sRouteIds}', $sRouteIds, static::getUrl('routes'));
        $aResult = static::doRemoteCall($sUrl);

        // Parsing data
        $aRoutes = [];
        foreach ($aResult as $aTmpRoute) {
            $aRoutes[$aTmpRoute['id']] = [
                'id' => $aTmpRoute['id'],
                'shortName' => $aTmpRoute['shortName'],
                'longName' => $aTmpRoute['longName'],
                'backgroundColor' => $aTmpRoute['color'],
                'foregroundColor' => $aTmpRoute['textColor']
            ];
        }

        return $aRoutes;
    }

    /**
     * Returns an array of stops in a cluster with their stoptimes
     *
     * @todo Data need to be converted to the app format
     * @param string $sClusterId
     * @return array
     */
    public static function getClusterTimes($sClusterId) {
        // Retrieving data
        $sUrl = str_replace('{sClusterId}', $sClusterId, static::getUrl('clustertimes'));
        $aResult = static::doRemoteCall($sUrl);

        return $aResult;
    }

    /**
     * Returns an array of timestamp for the next stop times
     *
     * @param string $sStopId
     * @return array
     * @throws Exception
     */
    public static function getStopTimes($sStopId) {
        // Retrieving data
        $sUrl = str_replace('{sStopId}', $sStopId, static::getUrl('stoptimes'));
        $aResult = static::doRemoteCall($sUrl);
        
        // Parsing data
        $aStopTimes = [];
        try {
            foreach ($aResult as $aLine) {
                // Skip xxx:yyy:1 as this seems to be a glitch in the API since a recent update.
                $aLineIdParts = explode(':', $aLine['pattern']['id']);
                if(isset($aLineIdParts[2]) && $aLineIdParts[2] === '1')
                {
                    continue;
                }

                $aLineTimes = [
                    'direction' => $aLine['pattern']['dir'],
                    'description' => StringHelper::toCamelCase($aLine['pattern']['desc'], false),
                    'times' => []
                ];
                
                foreach ($aLine['times'] as $aTime) {
                    // Formatting remaining time
                    // - Making timestamp for stop time
                    $sTimestamp = $aTime['serviceDay'] + (($aTime['realtime'] === true) ? $aTime['realtimeDeparture'] : $aTime['scheduledDeparture']);
                    // - Converting to DateTime to manipulate it easily
                    $oDatetime = DatetimeHelper::makeDatetimeFromTimestamp($sTimestamp, false);
                    // - Computing delta time
                    $iSecondsToDatetime = abs($oDatetime->getTimestamp() - time());
                    if ($iSecondsToDatetime >= 60 * 60) {
                        $sIntervalFormat = '%hh%I';
                    } elseif ($iSecondsToDatetime >= 60) {
                        $sIntervalFormat = '%imin';
                    } else {
                        $sIntervalFormat = '<1min';
                    }

                    // Adding remaining time
                    $aLineTimes['times'][] = [
                        'departure' => [
                            'datetime' => $oDatetime,
                            'timestamp' => $oDatetime->getTimestamp(),
                            'interval' => date_diff(new DateTime(), $oDatetime)->format($sIntervalFormat),
                            'realtime' => ($aTime['realtime'] === true)
                        ]
                    ];

                    // Updating stop name
                    $sRawStopName = $aTime['stopName'];
                    $sStopName = StringHelper::toCamelCase(substr($sRawStopName, strpos($sRawStopName, ',')+2), false);
                    $aLineTimes['name'] = $sStopName;
                }

                $aStopTimes[$aLine['pattern']['id']] = $aLineTimes;
            }
        } catch (Exception $e) {

            throw new Exception('Could not retrieve any stop times for "' . $sStopId . '". ' . var_dump($aResult));
        }
        
        return $aStopTimes;
    }

    /**
     * Do the remote call to $sUrl and return the response as an array
     *
     * @param string $sUrl
     * @return array
     */
    private static function doRemoteCall($sUrl) {
        // Initiating curl
        $oCh = curl_init();
        // Disabling SSL verification
        curl_setopt($oCh, CURLOPT_SSL_VERIFYPEER, false);
        // Returning response instead of printing it
        curl_setopt($oCh, CURLOPT_RETURNTRANSFER, true);
        // Setting URL
        curl_setopt($oCh, CURLOPT_URL, $sUrl);
        // Doing request
        $oResponse = curl_exec($oCh);
        // Closing connection
        curl_close($oCh);

        return json_decode($oResponse, true);
    }

    /**
     * Returns the complete URL of an API method corresponding to the $sUrlId
     *
     * @param string $sUrlId
     * @return string
     * @throws Exception
     */
    private static function getUrl($sUrlId) {
        if (!array_key_exists($sUrlId, static::$aUrls)) {
            throw new Exception('Could not found URL for "' . $sUrlId . '" in Metromobilite API');
        }

        return static::$sBaseUrl . static::$aUrls[$sUrlId];
    }

    
}
