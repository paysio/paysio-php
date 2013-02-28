<?php

namespace Paysio;

use \Paysio\Api\AbstractResource;

class Coupon extends AbstractResource
{
    /**
     * @static
     * @param $code
     * @return Coupon
     */
    public static function check($code)
    {
        $resource = static::createObject();
        $url = $resource->_getUrlPath() . '/code/' . $code . '/check';
        $response = $resource->_getApi()->getClient()->get($url);
        return $resource->populate($resource->_handleResponse($response));
    }
}