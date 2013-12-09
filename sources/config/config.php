<?php // sources/config/config.php.dist

// This file should be copied as config.php in the config directory and filled
// with your configuration parameters.

// pomm database configuration
$app['config.pomm.dsn'] = array(
    'prod' => array(), 
    'dev' => array('my_db' => array('dsn' => 'pgsql://greg/greg', 'name' => 'pragmafabrik', 'class' => '\Model\PragmaDatabase'))
);

// put your configuration here
