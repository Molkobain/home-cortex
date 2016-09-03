<?php

// Copyright (C) 2016 Guillaume Lajarige
//
// lajarige.guillaume@free.fr
// https://github.com/Molkobain

namespace Molkobain\HomeCortex\Helper\Weather;

use Exception;

/**
 * MetromibiliteAPIHelper is based on http://www.metromobilite.fr APIs
 * It allows to retrieve informations on transportation in the Grenoble, FR area
 *
 * More informations on http://www.metromobilite.fr/pages/opendata/OpenDataApi.html
 */
class OpenWeatherMapAPIHelper {

    const ENUM_SEARCH_MODE_NAME = 1;
    const ENUM_SEARCH_MODE_COORDINATES = 2;
    const ENUM_UNITS_METRIC = 'metric';
    const ENUM_UNITS_IMPERIAL = 'imperial';
    const DEFAULT_SEARCH_MODE = self::ENUM_SEARCH_MODE_NAME;
    const DEFAULT_LOCALE = 'en';
    const DEFAULT_UNITS = 'metrics';

    public static $sBaseUrl = 'http://api.openweathermap.org/data/2.5/';
    public static $aUrls = array(
        'today' => 'weather'
    );

    public static $sApiKey = null;
    public static $sLocale = self::DEFAULT_LOCALE;
    public static $sUnits = self::DEFAULT_UNITS;

    public static function setApiKey($sApiKey) {
        static::$sApiKey = $sApiKey;
    }

    public static function setLocale($sLocale) {
        static::$sLocale = $sLocale;
    }

    public static function setUnits($sUnits) {
        static::$sUnits = $sUnits;
    }

    /**
     * Returns an array of routes
     *
     * @param mixed $routeIds Route ids can be either an array (eg. array('SEM:C3', 'SEM:C4')) or a string (eg. 'SEM:C4')
     * @return array
     */
    public static function getToday($sMode = self::DEFAULT_SEARCH_MODE, $value) {
        // Parsing parameters
        if ($sMode === static::ENUM_SEARCH_MODE_NAME) {
            $sSearchParam = 'q=' . $value;
        } elseif ($sMode === static::ENUM_SEARCH_MODE_COORDINATES) {
            if (!is_array($value) || !array_key_exists('latitude', $value) || !array_key_exists('longitude', $value)) {
                throw new Exception('WeatherAPI : search coordinates are not a valid array. Given : ' . $value);
            }
            $sSearchParam = 'lat=' . $value['latitude'] . '&lon=' . $value['longitude'];
        } else {
            throw new Exception('WeatherAPI : search mode could not be recognized. Given : ' . $sMode);
        }

        // Retrieving data
        $sUrl = static::getUrl('today');
        // - Adding them to url
        if (strpos($sUrl, '?') === false) {
            $sUrl .= '?' . $sSearchParam;
        } else {
            $sUrl .= '&' . $sSearchParam;
        }
        $aResult = static::doRemoteCall($sUrl);

        // Parsing data
        $aForecast = array(
            'temperatures' => array(
                'current' => round($aResult['main']['temp']),
                'min' => round($aResult['main']['temp_min']),
                'max' => round($aResult['main']['temp_max'])
            ),
            'humidity' => $aResult['main']['humidity'] / 100,
            'conditions' => array(
                'description' => ucfirst($aResult['weather'][0]['description']),
                'icon' => static::findIconFromCode($aResult['weather'][0]['icon'])
            )
        );

        return $aForecast;
    }

    /**
     * Return the code of the icon from the local set that matches $sCode
     *
     * @param string $sCode
     * @return string The local icon code that matches $sIcon
     */
    private static function findIconFromCode($sCode) {
        // TODO : Find night icons
        $aIconsMap = array(
            '01d' => 'sunny',
            '02d' => 'sunny_s_cloudy',
            '03d' => 'partly_cloudy',
            '04d' => 'cloudy',
            '09d' => 'rain_light', // Note : This might be changed for 'rain' when we have found the lightning icon
            '10d' => 'sunny_s_rain',
            '11d' => 'rain',
            '13d' => 'snow',
            '50d' => 'fog',
            '01n' => 'sunny',
            '02n' => 'sunny_s_cloudy',
            '03n' => 'partly_cloudy',
            '04n' => 'cloudy',
            '09n' => 'rain_light', // Note : This might be changed for 'rain' when we have found the lightning icon
            '10n' => 'sunny_s_rain',
            '11n' => 'rain',
            '13n' => 'snow',
            '50n' => 'fog',
        );

        if (!array_key_exists($sCode, $aIconsMap)) {
            throw new Exception('OpenWeatherMapAPI : Could not find a matching icon for "' . $sCode . '"');
        }

        return $aIconsMap[$sCode];
    }

    /**
     * Do the remote call to $sUrl and return the response as an array
     *
     * @param string $sUrl
     * @return array
     */
    private static function doRemoteCall($sUrl) {
        // Making URL
        // - Preparing common parameters
        $sUrlParams = 'appid=' . static::$sApiKey . '&lang=' . static::$sLocale . '&units=' . static::$sUnits;
        // - Adding them to url
        if (strpos($sUrl, '?') === false) {
            $sUrl .= '?' . $sUrlParams;
        } else {
            $sUrl .= '&' . $sUrlParams;
        }
        
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
            throw new Exception('Could not found URL for "' . $sUrlId . '" in OpenWeatherMap API');
        }

        return static::$sBaseUrl . static::$aUrls[$sUrlId];
    }

    
}

?>
