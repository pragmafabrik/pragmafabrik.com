<?php // sources/application.php

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

$app = require "bootstrap.php";
$app->mount('/{lang}/', new \Controller\MainController());
$app->mount('/{lang}/blog', new \Controller\BlogController());

$app->get('/', function() use ($app) { return $app->redirect($app['url_generator']->generate('main_index', [ 'lang' => 'fr' ])); })->bind('index');

$app->error(function(Exception $e, $code) use ($app) {

    if ($app['debug'])
    {
        return;
    }

    return new Response($app['twig']->render('error.html.twig', array('message' => 'An error occured.')), $code);
});
return $app;
