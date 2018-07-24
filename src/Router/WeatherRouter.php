<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Router;

/**
 * Class WeatherRouter
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class WeatherRouter extends AbstractRouter {

    static $aRoutes = [
        ['pattern' => '/weather/forecast/today',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\WeatherController::forecastTodayAction',
            'bind' => 'm_weather_forecast_today'
        ],
    ];

}
