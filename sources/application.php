<?php // sources/application.php

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

$app = require "bootstrap.php";
$app->mount('/', new \Controller\MainController());

$app->error(function(Exception $e, $code) use ($app) {

    if ($app['debug'])
    {
        return;
    }

    return new Response($app['twig']->render('error.html.twig', array('message' => 'An error occured.')), $code);
});
return $app;
