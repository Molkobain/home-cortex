<?php

// Copyright (C) 2016 Guillaume Lajarige
//
// lajarige.guillaume@free.fr
// https://github.com/Molkobain

namespace Molkobain\HomeCortex\Controller;

use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Molkobain\HomeCortex\Controller\AbstractController;
use Molkobain\HomeCortex\Helper\Weather\WeatherUndergroundAPIHelper;

class WeatherController extends AbstractController {

    public function forecastTodayAction(Request $oRequest, Application $oApp) {
        // Note : This could be passed with JS format so the client knows how to handle it
        $aData = array(
        );

        $aCoordinates = array('latitude' => $oApp['parameters']['localisation']['latitude'], 'longitude' => $oApp['parameters']['localisation']['longitude']);
        //$aResult = OpenWeatherMapAPIHelper::getToday($aCoordinates, OpenWeatherMapAPIHelper::ENUM_SEARCH_MODE_COORDINATES);
        $aResult = WeatherUndergroundAPIHelper::getToday($aCoordinates, WeatherUndergroundAPIHelper::ENUM_SEARCH_MODE_COORDINATES);

        // Preparing response data
        $aData['forecast'] = $aResult;
        $sTemplate = 'weather/widget.html.twig';

        return $oApp['twig']->render($sTemplate, $aData);
    }

}

?>