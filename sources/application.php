<?php // sources/application.php

use \Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;

$app = require "bootstrap.php";
$app->mount('/{lang}/', new \Controller\MainController());
//$app->mount('/{lang}/blog', new \Controller\BlogController());

$app->get('/', function() use ($app) { return $app->redirect($app['url_generator']->generate('main_index', [ 'lang' => 'fr' ])); })->bind('index');

$app->error(function(Exception $e, $code) use ($app) {

    if ($app['debug'])
    {
        return;
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

    $errors = [ 
        '404' => [ 'fr' => 'La ressource demandée n\'existe pas.', 'en' => 'The content you are asking does not exist.' ], 
        '500' => [ 'fr' => 'Un problème technique empêche l\'affichage du contenu demandé.', 'en' => 'A technical problem prevents us from displaying the content you asked.' ],
        ];

    $lang = $app['request']->get('lang', 'fr');

    try
    {
        return new Response($app['twig']->render(sprintf("%s/error.html.twig", $lang), [ 'message' => $errors[$code][$lang] ]), $code);
    }
    catch(\Exception $e)
    {
        return $e->getMessage();
    }
});
return $app;
