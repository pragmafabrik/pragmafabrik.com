<?php // bin/generate_model.php

$app = require(__DIR__."/../sources/bootstrap.php");

$scan = new Pomm\Tools\ScanSchemaTool(array(
    'schema'     => $argv[1],
    'database'   => $app['pomm']->getDatabase(),
    'prefix_dir' => PROJECT_DIR."/sources/lib",
    'namespace'  => 'Model\%dbname%\%schema%',
    ));
$scan->execute();
$scan->getOutputStack()->setLevel(254);

foreach ( $scan->getOutputStack() as $line )
{
    printf("%s\n", $line);
}

