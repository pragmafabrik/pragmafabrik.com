<?php #web/index.php

$filename = __DIR__.preg_replace('#(\?.*)$#', '', $_SERVER['REQUEST_URI']);
if (php_sapi_name() === 'cli-server' && is_file($filename)) {
    return false;
}

$app = require dirname(__DIR__).'/sources/application.php';

$app->run();

if ($app['debug'] === true)
{
    $app['monolog']->addInfo(sprintf("SwiftMailer: %s", $app['swiftmailer.logger']->dump()));
}
