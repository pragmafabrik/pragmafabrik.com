<?php #sources/bootstrap.php

use Monolog\Logger;
use Monolog\Handler\StreamHandler;

// This script sets up the application DI with services.

if (!defined('PROJECT_DIR'))
{
    define('PROJECT_DIR', dirname(__DIR__));
}

require PROJECT_DIR.'/sources/config/environment.php';

// autoloader
$loader = require PROJECT_DIR.'/vendor/autoload.php';
$loader->add(false, PROJECT_DIR.'/sources/lib');

$app = new Silex\Application();

// configuration parameters
if (!file_exists(PROJECT_DIR.'/sources/config/config.php')) {
    throw new \RunTimeException("No config.php found in config.");
}

require PROJECT_DIR.'/sources/config/config.php';

// extensions registration

use Silex\Provider;

$app->register(new Provider\UrlGeneratorServiceProvider());
$app->register(new Provider\SessionServiceProvider());
$app->register(new Provider\TwigServiceProvider(), array(
    'twig.path' => array(PROJECT_DIR.'/sources/twig'),
));
$app->register(new \Pomm\Silex\PommServiceProvider(), array(
    'pomm.databases' => $app['config.pomm.dsn'][ENV]
));

// Service container customization. 
$app['loader'] = $loader;
$app['pomm.connection'] = $app->share(function() use ($app) { return $app['pomm']
    ->getDatabase()
    ->createConnection();
    });

// set DEBUG mode or not
if (preg_match('/^dev/', ENV))
{
    $app['debug'] = true;
    $app['logger'] = $app->share(function() use ($app) { $log = new Logger('app'); $log->pushHandler(new StreamHandler(PROJECT_DIR.'/logs/application.log')); return $log; });
    $app['pomm.connection'] = $app->share($app->extend('pomm.connection', function($connection, $app) { $connection->setLogger($app['logger']); return $connection; }));
}

return $app;
