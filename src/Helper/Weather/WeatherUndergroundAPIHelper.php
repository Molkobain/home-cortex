<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Helper\Weather;

use Exception;

/**
 * WeatherUndergroundAPIHelper is based on https://www.wunderground.com/weather/api/ APIs
 * It allows to retrieve informations on the weather around the world
 *
 * More informations on https://www.wunderground.com/weather/api/
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class WeatherUndergroundAPIHelper {

    const ENUM_SEARCH_MODE_NAME = 1;
    const ENUM_SEARCH_MODE_COORDINATES = 2;
//    const ENUM_UNITS_METRIC = 'metric';
//    const ENUM_UNITS_IMPERIAL = 'imperial';
    const DEFAULT_SEARCH_MODE = self::ENUM_SEARCH_MODE_NAME;
    const DEFAULT_LOCALE = 'EN';
//    const DEFAULT_UNITS = 'metrics';

    public static $sBaseUrl = 'http://api.wunderground.com/api/{sApiKey}/lang:{sLocale}/';
    public static $aUrls = [
        'today' => 'conditions/'
    ];

    public static $sApiKey = null;
    public static $sLocale = self::DEFAULT_LOCALE;
//    public static $sUnits = self::DEFAULT_UNITS;

    public static function setApiKey($sApiKey) {
        static::$sApiKey = $sApiKey;
    }

    public static function setLocale($sLocale) {
        $aLocaleExploded = explode('_', $sLocale);
        $sWULocale = (isset($aLocaleExploded[0])) ? $aLocaleExploded[0] : $sLocale;
        $sWULocale = ($sWULocale !== null) ? $sWULocale : static::DEFAULT_LOCALE;
        $sWULocale = strtoupper(substr($sWULocale, 0, 2));
        static::$sLocale = $sWULocale;
    }

//    public static function setUnits($sUnits) {
//        static::$sUnits = $sUnits;
//    }

    /**
     * Returns weather conditions for today in the city defined by $value
     *
     * @param mixed $value City to get conditions for, can be either an array of latitude/longitude or country/city
     * @return array
     */
    public static function getToday($value, $sMode = self::DEFAULT_SEARCH_MODE) {
        // Parsing parameters
        if ($sMode === static::ENUM_SEARCH_MODE_NAME) {
            if (!is_array($value) || !array_key_exists('country', $value) || !array_key_exists('city', $value)) {
                throw new Exception('WeatherAPI : search city are not a valid array. Given : ' . $value);
            }
            $sSearchParam = 'q/' . $value['country'] . '/' . $value['city'];
        } elseif ($sMode === static::ENUM_SEARCH_MODE_COORDINATES) {
            if (!is_array($value) || !array_key_exists('latitude', $value) || !array_key_exists('longitude', $value)) {
                throw new Exception('WeatherAPI : search coordinates are not a valid array. Given : ' . $value);
            }
            $sSearchParam = 'q/' . $value['latitude'] . ',' . $value['longitude'];
        } else {
            throw new Exception('WeatherAPI : search mode could not be recognized. Given : ' . $sMode);
        }

        // Retrieving data
        $sUrl = static::getUrl('today') . $sSearchParam . '.json';
        $aResult = static::doRemoteCall($sUrl);

        // Parsing data
        $aForecast = [
            'temperatures' => [
                'current' => round($aResult['current_observation']['temp_c']),
                'min' => round($aResult['current_observation']['temp_c']),
                'max' => round($aResult['current_observation']['temp_c'])
            ],
            'conditions' => [
                'description' => ucfirst($aResult['current_observation']['weather']),
                'icon' => static::findIconFromCode($aResult['current_observation']['icon'])
            ]
        ];

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
        $aIconsMap = [
            'clear' => 'sunny',
            'cloudy' => 'cloudy',
            'flurries' => 'rain', // Note : This might be changed when we have found the flurries icon
            'fog' => 'fog',
            'hazy' => 'mist',
            'mostlycloudy' => 'cloudy_s_sunny',
            'mostlysunny' => 'sunny_s_cloudy',
            'partlycloudy' => 'partly_cloudy',
            'partlysunny' => 'partly_cloudy',
            'sleet' => 'sleet',
            'rain' => 'rain',
            'snow' => 'snow',
            'sunny' => 'sunny',
            'tstorms' => 'thunderstorms', // Note : This might be changed when we have found the lightning icon
            'unknown' => 'partly_cloudy',
        ];

        if (!array_key_exists($sCode, $aIconsMap)) {
            throw new Exception('WeatherUndergroundAPI : Could not find a matching icon for "' . $sCode . '"');
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
        $sUrl = str_replace('{sApiKey}', static::$sApiKey, $sUrl);
        $sUrl = str_replace('{sLocale}', static::$sLocale, $sUrl);

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

