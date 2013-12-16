<?php #sources/bootstrap.php

use Silex\Provider;

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

$app->register(new Provider\UrlGeneratorServiceProvider());
$app->register(new Provider\SessionServiceProvider());
$app->register(new Provider\FormServiceProvider());
$app->register(new Provider\ValidatorServiceProvider());
$app->register(new Provider\SwiftmailerServiceProvider(), array('swiftmailer.options' => $app['config.swiftmailer']));
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
    ->getConnection();
    });

// set DEBUG mode or not
if (preg_match('/^dev/', ENV))
{
    $app['debug'] = true;
    $app['swiftmailer.transport'] = $app->share(function() { return Swift_NullTransport::newInstance(); });
    $app['swiftmailer.logger'] = $app->share(function() { return new Swift_Plugins_MessageLogger(); });
    $app['mailer'] = $app->share($app->extend('mailer', function($mailer, $app) {
        $mailer->registerPlugin($app['swiftmailer.logger']);
        return $mailer;
    }));
    $app->register(new Provider\MonologServiceProvider(), array('monolog.logfile' => PROJECT_DIR.'/logs/application.log'));
    $app['pomm.connection'] = $app->share($app->extend('pomm.connection', function($connection, $app) { $connection->setLogger($app['monolog']); return $connection; }));
    $app['twig']->addExtension(new Twig_Extension_Debug());
}

return $app;
