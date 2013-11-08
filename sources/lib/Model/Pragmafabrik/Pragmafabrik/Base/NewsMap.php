<?php

namespace Model\Pragmafabrik\Pragmafabrik\Base;

use \Pomm\Object\BaseObjectMap;
use \Pomm\Exception\Exception;

abstract class NewsMap extends BaseObjectMap
{
    public function initialize()
    {

        $this->object_class =  'Model\Pragmafabrik\Pragmafabrik\News';
        $this->object_name  =  'pragmafabrik.news';

        $this->addField('slug', 'varchar');
        $this->addField('title', 'varchar');
        $this->addField('content', 'text');
        $this->addField('created_at', 'timestamp');
        $this->addField('tags', 'public.ltree[]');
        $this->addField('ft_vector', 'tsvector');

        $this->pk_fields = array('slug');
    }
}
