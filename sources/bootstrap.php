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
$app->register(new Silex\Provider\TranslationServiceProvider(), array(
        'locale_fallbacks' => ['fr'],
    ));
$app->register(new Provider\ValidatorServiceProvider());
$app->register(new Provider\SwiftmailerServiceProvider(), array('swiftmailer.options' => $app['config.swiftmailer']));
$app->register(new Provider\TwigServiceProvider(), array(
    'twig.path' => array(PROJECT_DIR.'/sources/twig'),
));

// Service container customization. 
$app['loader'] = $loader;

// set DEBUG mode or not
if (preg_match('/^dev/', ENV))
{
    $app['debug'] = true;
    $app['swiftmailer.transport'] = $app->share(function() { return Swift_NullTransport::newInstance(); });
    $app['swiftmailer.logger'] = $app->share(function() { return new Swift_Plugins_Loggers_ArrayLogger(); });
    $app['mailer'] = $app->share($app->extend('mailer', function($mailer, $app) {
        $mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($app['swiftmailer.logger']));
        return $mailer;
    }));
    $app->register(new Provider\MonologServiceProvider(), array('monolog.logfile' => PROJECT_DIR.'/logs/application.log'));
    $app->extend('twig', function($twig, $app) { $twig->addExtension(new Twig_Extension_Debug()); return $twig; });
    $app['twig'] = $app->share($app->extend('twig', function($twig, $app) { $twig->addExtension(new Twig_Extension_Debug()); return $twig; }));
}

// translations
$app['translator.domains'] = [
    'messages' => [
        'fr' => [
            'error.404' => 'La ressource demandée n\'existe pas.',
            'contact.success' => 'Votre demande de contact a été envoyée',
            'legal.notice' => 'mentions légales',
            ],
        'en' => [
            'error.404' => 'The content you are asking does not exist.',
            'contact.success' => 'Your contact request has been sent',
            'legal.notice' => 'Legal notice',
            ]
        ],
    'validators' => [
        'fr' => [
            'contact.name.not_blank' => 'Veuillez saisir votre nom',
            'contact.email.not_blank' => 'Veuillez saisir votre email',
            'contact.email.invalid' => 'L\'email saisie est invalide',
            'contact.message.not_blank' => 'Veuillez saisir un message',
            ],
        'en' => [
            'contact.name.not_blank' => 'Please indicate your name',
            'contact.email.not_blank' => 'Please indicate your email',
            'contact.email.invalid' => 'Invalid email address',
            'contact.message.not_blank' => 'Please let a message',
            ]
        ]
    ];

return $app;
