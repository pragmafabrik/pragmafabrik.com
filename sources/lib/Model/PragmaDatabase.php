<?php

namespace Model;

use \Pomm\Connection\Database;
use \Pomm\Converter;

class PragmaDatabase extends Database
{
    protected function initialize()
    {
        parent::initialize();

        $this->registerConverter('LTree', new Converter\PgLTree(), ['public.ltree', 'ltree']);
    }
}
