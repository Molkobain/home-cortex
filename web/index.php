<?php

// Copyright (C) 2016 Guillaume Lajarige
//
// lajarige.guillaume@free.fr
// https://github.com/Molkobain

// Initialize app
require_once __DIR__ . '/../app/bootstrap.php';
// Silex framework and components
require_once APP_BASE_DIR . 'vendor/autoload.php'
;

// Application
use Molkobain\HomeCortex\Helper\ApplicationHelper;

// Initializing Silex framework
$oApp = new Silex\Application();

// Registring optional silex components
// - TWIG
$oApp->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => APP_BASE_DIR . 'src/View/'
));
// - HTTP Fragment
$oApp->register(new Silex\Provider\HttpFragmentServiceProvider());
// - YAML Config
$oApp->register(new Juuuuuu\Silex\YamlConfigServiceProvider(APP_BASE_DIR . 'app/config/config.yml'));

// Configuring Silex application
$oApp['debug'] = (isset($_REQUEST['debug']) && ($_REQUEST['debug'] === 'true') );

// Registering error/exception handler in order to transform php error to exception
ApplicationHelper::registerExceptionHandler($oApp);
// Preparing portal foundations (Can't use Silex autoload through composer as we don't follow PSR conventions -filenames, functions-)
ApplicationHelper::loadAppVariables($oApp);
ApplicationHelper::loadRouters();
ApplicationHelper::registerRoutes($oApp);
ApplicationHelper::registerTwigExtensions($oApp);
ApplicationHelper::loadAppProperties($oApp);
ApplicationHelper::registerWeatherApi($oApp);

// Running application
$oApp->run();