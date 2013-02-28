<?php

namespace Paysio;

use \Paysio\Api\AbstractResource;

class Customer extends AbstractResource
{
        /**
     * @param array $params
     * @return AbstractResource
     */
    public static function create(array $params = null)
    {
        $resource = static::createObject();
        return $resource->_create($params);
    }
}