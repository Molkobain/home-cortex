<?php

// Copyright (C) 2016 Guillaume Lajarige
//
// lajarige.guillaume@free.fr
// https://github.com/Molkobain

namespace Molkobain\HomeCortex\Router;

use Molkobain\HomeCortex\Router\AbstractRouter;

class WeatherRouter extends AbstractRouter {

    static $aRoutes = array(
        array('pattern' => '/weather/forecast/today',
            'callback' => 'Molkobain\\HomeCortex\\Controller\\WeatherController::forecastTodayAction',
            'bind' => 'weather_forecast_today'
        ),
    );

}

?>
