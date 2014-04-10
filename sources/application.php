<?php // sources/application.php

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

$app = require "bootstrap.php";
$app->mount('/{_locale}/', new \Controller\MainController());

$app->get('/', function() use ($app) { return $app->redirect($app['url_generator']->generate('main_index', [ '_locale' => 'fr' ])); })->bind('index');

$app->error(function(Exception $e, $code) use ($app) {


    if ($app['debug'])
    {
        //return;
    }

    if ($code == '500')
    {
        $app['mailer']->send(
            \Swift_Message::newInstance()
            ->setSubject(sprintf('[pragmafabrik.com] Erreur 500'))
            ->setFrom("noreply@pragmafabrik.com")
            ->setSender($app['config.swiftmailer']['destination'])
            ->setTo([$app['config.swiftmailer']['destination']])
            ->setBody(sprintf("Date='%s'\nUrl='%s' (%s)\nMessage='%s'\n", date('d-m-Y H:i:s'), $app['request']->getRequestUri(), $app['request']->getRealMethod(), $e->getMessage()))
        );
    }
    elseif ($code == "404")
    {
        if (preg_match('#/(fr|en)/.*#', $app['request']->getUri(), $matchs))
        {
            $app['locale'] = $matchs[1];
        }
        else
        {
            $app['locale'] = 'fr';
        }
    }

    try
    {
        return new Response($app['twig']->render(sprintf("%s/error.html.twig", $app['locale']), [ 'message' => sprintf("error.%s", $code) ]), $code);
    }
    catch(\Exception $e)
    {
        return $e->getMessage();
    }
});
return $app;
