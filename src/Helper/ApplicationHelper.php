<?php

// Copyright (C) 2016 Guillaume Lajarige
// https://github.com/Molkobain
//
// This file is part of an open-source project

namespace Molkobain\HomeCortex\Helper;

use Exception;
use Silex\Application;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\ExceptionHandler;
use Twig_SimpleFilter;
use Molkobain\HomeCortex\Helper\Weather\WeatherUndergroundAPIHelper;

/**
 * Contains static methods to help loading / registering classes of the application.
 * Mostly used for Controllers / Routers / Entities initialization.
 *
 * @author Guillaume Lajarige <lajarige.guillaume@free.fr>
 */
class ApplicationHelper {

    /**
     * Loads some application variables such as the root directory, base url, ...
     *
     * @param \Silex\Application $oApp
     */
    public static function loadAppVariables(Application $oApp) {
        // Global variables
        $oApp['instance.base_dir'] = APP_BASE_DIR;
        //$oApp['instance.base_url'] = APP_BASE_URL;
        $oApp['instance.base_url'] = $oApp['parameters']['app']['base_url'];
        $oApp['instance.version'] = APP_VERSION;

        // Timezone
        $sTimezone = 'UTC';
        try {
            $sConfigTimezone = $oApp['parameters']['date']['timezone'];
            if ($sConfigTimezone !== null && $sConfigTimezone !== '') {
                $sTimezone = $sConfigTimezone;
            }
        } catch (Exception $e) {
            // Do nothing, it is already set to the default value
        }
        ini_set('date.timezone', $sTimezone);

        // Locale
        $sLocale = 'en_EN';
        try {
            $sConfigLocale = $oApp['parameters']['locale'];
            if ($sConfigLocale !== null && $sConfigLocale !== '') {
                $sLocale = $sConfigLocale;
            }
        } catch (Exception $e) {
            // Keep the default value and put it to the parameters
            $oApp['parameters']['locale'] = $sLocale;
        }
        setlocale(LC_TIME, $sLocale);
    }

    /**
     * Loads classes from the base portal
     *
     * @param string $sScannedDir Directory to load the files from
     * @param string $sFilePattern Pattern of files to load
     * @param string $sType Type of files to load, used only in the Exception message, can be anything
     * @throws \Exception
     */
    public static function loadClasses($sScannedDir, $sFilePattern, $sType) {
        // Loading classes from base portal
        foreach (scandir($sScannedDir) as $sFile) {
            if (strpos($sFile, $sFilePattern) !== false && file_exists($sFilepath = $sScannedDir . '/' . $sFile)) {
                try {
                    require_once $sFilepath;
                } catch (Exception $e) {
                    throw new Exception('Error while trying to load ' . $sType . ' ' . $sFile);
                }
            }
        }
    }

    /**
     * Loads routers from the base portal
     *
     * @param string $sScannedDir Directory to load the routers from
     * @throws \Exception
     */
    public static function loadRouters($sScannedDir = null) {
        if ($sScannedDir === null) {
            $sScannedDir = __DIR__ . '/../Router';
        }

        // Loading routers from base portal (those from modules have already been loaded by module.xxx.php files)
        self::LoadClasses($sScannedDir, 'Router.php', 'router');
    }

    /**
     * Registers routes in the Silex Application from all declared Router classes
     *
     * @param \Silex\Application $oApp
     * @throws \Exception
     */
    public static function registerRoutes(Application $oApp) {
        $aAllRoutes = [];

        foreach (get_declared_classes() as $sPHPClass) {
            if (is_subclass_of($sPHPClass, 'Molkobain\\HomeCortex\\Router\\AbstractRouter')) {
                try {
                    // Registering to Silex Application
                    $sPHPClass::RegisterAllRoutes($oApp);

                    // Registering them together so we can access them from everywhere
                    foreach ($sPHPClass::GetRoutes() as $aRoute) {
                        $aAllRoutes[$aRoute['bind']] = $aRoute;
                    }
                } catch (Exception $e) {
                    throw new Exception('Error while trying to register routes');
                }
            }
        }

        $oApp['instance.routes'] = $aAllRoutes;
    }

    /**
     * Returns all registered routes for the current portal instance
     *
     * @param \Silex\Application $oApp
     * @param boolean $bNamesOnly If set to true, function will return only the routes' names, not the objects
     * @return array
     */
    public static function getRoutes(Application $oApp, $bNamesOnly = false) {
        return ($bNamesOnly) ? array_keys($oApp['instance.routes']) : $oApp['instance.routes'];
    }

    /**
     * Registers Twig extensions such as filters or functions.
     * It allows us to access some stuff directly in twig.
     *
     * @param \Silex\Application $oApp
     */
    public static function registerTwigExtensions(Application $oApp) {
//        // Filters to enable base64 encode/decode
//        // Usage in twig : {{ 'String to encode'|base64_encode }}
//        $oApp['twig']->addFilter(new Twig_SimpleFilter('base64_encode', 'base64_encode'));
//        $oApp['twig']->addFilter(new Twig_SimpleFilter('base64_decode', 'base64_decode'));
//        // Filters to enable json decode  (encode already exists)
//        // Usage in twig : {{ aSomeArray|json_decode }}
//        $oApp['twig']->addFilter(new Twig_SimpleFilter('json_decode', function($sJsonString, $bAssoc = false) {
//                    return json_decode($sJsonString, $bAssoc);
//                })
//        );
    }

    /**
     * Registers an exception handler that will intercept controllers exceptions and display them in a nice template.
     * Note : It is only active when $oApp['debug'] is false
     *
     * @param Application $oApp
     */
    public static function registerExceptionHandler(Application $oApp) {
        ErrorHandler::register();
        ExceptionHandler::register(($oApp['debug'] === true));

        if (!$oApp['debug']) {
            $oApp->error(function(Exception $e, $code) use ($oApp) {
                        $aData = [
                            'exception' => $e,
                            'code' => $code,
                            'error_title' => '',
                            'error_message' => $e->getMessage()
                        ];

                        switch ($code) {
                            case 404:
                                $aData['error_title'] = Dict::S('Error:HTTP:404');
                                break;
                            default:
                                $aData['error_title'] = Dict::S('Error:HTTP:500');
                                break;
                        }

                        IssueLog::Error($aData['error_title'] . ' : ' . $aData['error_message']);

                        if ($oApp['request']->isXmlHttpRequest()) {
                            $oResponse = $oApp->json($aData, $code);
                        } else {
                            $oResponse = $oApp['twig']->render('itop-portal-base/portal/src/views/errors/layout.html.twig', $aData);
                        }

                        return $oResponse;
                    });
        }
    }

    public static function registerWeatherApi(Application $oApp) {
//        // OpenWeatherMap
//        try {
//            // - Api key
//            $sOWMApiKey = $oApp['parameters']['weather_providers']['openweathermap']['api_key'];
//            OpenWeatherMapAPIHelper::setApiKey($sOWMApiKey);
//            // - Locale
//            // TODO : This should be done in the helper like for WeatherUndergroundAPIHelper
//            $aLocaleExploded = explode('_', $oApp['parameters']['locale']);
//            $sOWMLocale = (isset($aLocaleExploded[0])) ? $aLocaleExploded[0] : 'en';
//            $sOWMLocale = substr($sOWMLocale, 0, 2);
//            OpenWeatherMapAPIHelper::setLocale($sOWMLocale);
//            // - Units
//            OpenWeatherMapAPIHelper::setUnits('metric');
//        } catch (Exception $e) {
//            // Do nothing
//        }
        // WeatherUnderground
        try {
            // - Api key
            $sWUApiKey = $oApp['parameters']['weather_providers']['weatherunderground']['api_key'];
            WeatherUndergroundAPIHelper::setApiKey($sWUApiKey);
            // - Locale
            $sWULocale = $oApp['parameters']['weather_providers']['weatherunderground']['api_key'];
            WeatherUndergroundAPIHelper::setLocale($oApp['parameters']['locale']);
        } catch (Exception $e) {
            // Do nothing
        }
    }

}

?>