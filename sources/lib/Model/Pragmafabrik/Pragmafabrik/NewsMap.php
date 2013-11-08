<?php

namespace Model\Pragmafabrik\Pragmafabrik;

use Model\Pragmafabrik\Pragmafabrik\Base\NewsMap as BaseNewsMap;
use Model\Pragmafabrik\Pragmafabrik\News;
use \Pomm\Exception\Exception;
use \Pomm\Query\Where;

class NewsMap extends BaseNewsMap
{
    public function getSelectFields($alias = null)
    {
        $fields = parent::getSelectFields($alias);
        unset($fields['ft_vector']);

        return $fields;
    }
}
