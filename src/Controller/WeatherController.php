<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Molkobain\HomeCortex\Helper\Weather\WeatherUndergroundAPIHelper;

/**
 * Class WeatherController
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class WeatherController extends AbstractController {

    /**
     * @param Symfony\Component\HttpFoundation\Request $oRequest
     * @param Silex\Application $oApp
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function forecastTodayAction(Request $oRequest, Application $oApp) {
        // Note : This could be passed with JS format so the client knows how to handle it
        $aData = [];

        $aCoordinates = ['latitude' => $oApp['parameters']['localisation']['latitude'], 'longitude' => $oApp['parameters']['localisation']['longitude']];
        //$aResult = OpenWeatherMapAPIHelper::getToday($aCoordinates, OpenWeatherMapAPIHelper::ENUM_SEARCH_MODE_COORDINATES);
        $aResult = WeatherUndergroundAPIHelper::getToday($aCoordinates, WeatherUndergroundAPIHelper::ENUM_SEARCH_MODE_COORDINATES);

        // Preparing response data
        $aData['forecast'] = $aResult;
        $sTemplate = 'weather/widget.html.twig';

        return $oApp['twig']->render($sTemplate, $aData);
    }

}
