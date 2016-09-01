<?php

// Copyright (C) 2016 Guillaume Lajarige
//
// lajarige.guillaume@free.fr
// https://github.com/Molkobain

namespace Molkobain\HomeCortex\Helper\Transport;

use DateTime;
use Molkobain\HomeCortex\Helper\DatetimeHelper;

/**
 * MetromibiliteAPIHelper is based on http://www.metromobilite.fr APIs
 * It allows to retrieve informations on transportation in the Grenoble, FR area
 *
 * More informations on http://www.metromobilite.fr/pages/opendata/OpenDataApi.html
 */
class MetromobiliteAPIHelper {

    public static $sBaseUrl = 'http://data.metromobilite.fr/';
    public static $aUrls = array(
        'routes' => 'api/routers/default/index/routes?codes={sRouteIds}',
        'clustertimes' => 'api/routers/default/index/clusters/{sClusterId}/stoptimes',
        'stoptimes' => 'api/routers/default/index/stops/{sStopId}/stoptimes'
    );

    /**
     * Returns an array of routes
     *
     * @param mixed $routeIds Route ids can be either an array (eg. array('SEM:C3', 'SEM:C4')) or a string (eg. 'SEM:C4')
     * @return array
     */
    public static function getRoutes($routeIds) {
        // Preparing query
        if (!is_array($routeIds)) {
            $routeIds = array($routeIds);
        }
        $sRouteIds = implode(',', $routeIds);

        // Retrieving data
        $sUrl = str_replace('{sRouteIds}', $sRouteIds, static::getUrl('routes'));
        $aResult = static::doRemoteCall($sUrl);

        // Parsing data
        $aRoutes = array();
        foreach ($aResult as $aTmpRoute) {
            $aRoutes[$aTmpRoute['id']] = array(
                'id' => $aTmpRoute['id'],
                'shortName' => $aTmpRoute['shortName'],
                'longName' => $aTmpRoute['longName'],
                'backgroundColor' => $aTmpRoute['color'],
                'foregroundColor' => $aTmpRoute['textColor']
            );
        }

        return $aRoutes;
    }

    /**
     * Returns an array of stops in a cluster with their stoptimes
     *
     * @todo Datas need to be converted to the app format
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
     */
    public static function getStopTimes($sStopId) {
        // Retrieving data
        $sUrl = str_replace('{sStopId}', $sStopId, static::getUrl('stoptimes'));
        $aResult = static::doRemoteCall($sUrl);
        
        // Parsing data
        $aStopTimes = array();
        try {
            foreach ($aResult as $aLine) {
                $aLineTimes = array(
                    'direction' => $aLine['pattern']['dir'],
                    'description' => $aLine['pattern']['desc'],
                    'times' => array()
                );

                foreach ($aLine['times'] as $aTime) {
                    $oDatetime = DatetimeHelper::makeDatetimeFromTimestamp(($aTime['realtime'] === true) ? $aTime['realtimeDeparture'] : $aTime['scheduledDeparture'], true);
                    $sIntervalFormat = ( abs($oDatetime->getTimestamp() - time()) >= 60 * 60 ) ? '%hh%I' : '%imin';

                    $aLineTimes['times'][] = array(
                        'departure' => array(
                            'datetime' => $oDatetime,
                            'timestamp' => $oDatetime->getTimestamp(),
                            'interval' => date_diff(new DateTime(), $oDatetime)->format($sIntervalFormat),
                            'realtime' => ($aTime['realtime'] === true)
                        )
                    );
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

?>
