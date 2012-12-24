<?php

namespace Paysio\Api;

class Client
{
    /**
     * @var \Paysio\Api
     */
    protected $_api;

    /**
     * @param \Paysio\Api $api
     */
    public function __construct(\Paysio\Api $api)
    {
        $this->_api = $api;
    }

    /**
     * @param $url
     * @param array $params
     * @param array $headers
     * @return Response
     */
    public function get($url, array $params = null, array $headers = array())
    {
        $options[CURLOPT_HTTPGET] = 1;
        if (count($params) > 0) {
            $url .= '?' . $this->_encode($params);
        }
        return $this->_request($url, $options, $headers);
    }

    /**
     * @param $url
     * @param array $params
     * @param array $headers
     * @return Response
     */
    public function post($url, array $params, array $headers = array())
    {
        $options[CURLOPT_POST] = 1;
        $options[CURLOPT_POSTFIELDS] = $this->_encode($params);
        return $this->_request($url, $options, $headers);
    }

    /**
     * @param $url
     * @param array $params
     * @param array $headers
     * @return Response
     */
    public function put($url, array $params, array $headers = array())
    {
        $options[CURLOPT_CUSTOMREQUEST] = 'PUT';
        $options[CURLOPT_POSTFIELDS] = $this->_encode($params);
        return $this->_request($url, $options, $headers);
    }

    /**
     * @param $url
     * @param array $params
     * @param array $headers
     * @return Response
     */
    public function delete($url, array $params = null, array $headers = array())
    {
        $options[CURLOPT_CUSTOMREQUEST] = 'DELETE';
        if (count($params) > 0) {
            $options[CURLOPT_POSTFIELDS] = $this->_encode($params);
        }
        return $this->_request($url, $options, $headers);
    }

    /**
     * @param $url
     * @param array $options
     * @param array $headers
     * @return Response
     * @throws Exception\ConnectionError
     */
    protected function _request($url, array $options, array $headers = array())
    {
        $ch = curl_init();

        $defaultOptions = array(
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_USERAGENT => 'PaysioPhp',
            CURLOPT_USERPWD => $this->_api->getKey() . ':',
            CURLOPT_HEADER => 1,
        );
        $options = $defaultOptions + $options;

        $urlPrefix = strpos($url, 'http') === 0 ? '' : $this->_api->getEndpoint();
        $options[CURLOPT_URL] = utf8_encode($urlPrefix . '/' . trim($url, '/'));

        if ($headers) {
            $options[CURLOPT_HTTPHEADER] = $headers;
        }

        curl_setopt_array($ch, $options);

        $response = curl_exec($ch);
        if ($response === false) {
            throw new Exception\ConnectionError(curl_error($ch), curl_errno($ch));
        }

        $info = curl_getinfo($ch);

        $header = substr($response, 0, $info['header_size']);
        $body = substr($response, $info['header_size']);

        preg_match("/Location: (.*?)\n/", $header, $matches);
        $location = isset($matches[1]) ? trim($matches[1]) : null;

        return new Response($body, $info['http_code'], $location, $this->_api->getEncoding());
    }

    /**
     * @param array $params
     * @return string
     */
    protected function _encode(array $params)
    {
        if (stripos($this->_api->getEncoding(), 'UTF-8') === false) {
            array_walk_recursive($params, function(&$val, $key, $encoding) {
                $val = iconv($encoding, 'UTF-8', $val);
            }, $this->_api->getEncoding());
        }

        return http_build_query($params, null, '&');
    }
}
