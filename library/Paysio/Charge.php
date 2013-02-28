<?php

namespace Paysio;

use \Paysio\Api\AbstractResource;

class Charge extends AbstractResource
{
    /**
     * @static
     * @param array $params
     * @return Charge
     */
    public static function create(array $params)
    {
        $ip = isset($params['ip']) ? $params['ip'] : $_SERVER['REMOTE_ADDR'];
        $headers = array('X-Real-IP: ' . $ip);

        $resource = static::createObject();
        $url = $resource->_getUrlPath();
        $response = $resource->_getApi()->getClient()->post($url, $params, $headers);
        return $resource->populate($resource->_handleResponse($response));
    }

    /**
     * @param array $params
     * @return Charge
     */
    public function refund(array $params = null)
    {
        $params = (array)$params;
        $url = $this->_getUrlPath() . '/' . $this->id . '/refund';
        $response = $this->_getApi()->getClient()->post($url, $params);
        return $this->populate($this->_handleResponse($response));
    }

    /**
     * @return Charge
     */
    public function invoice()
    {
        $url = $this->_getUrlPath() . '/' . $this->id . '/invoice';
        $response = $this->_getApi()->getClient()->get($url);
        return $this->populate($this->_handleResponse($response));
    }
}