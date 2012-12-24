<?php

namespace Paysio\Api;

class Response
{
    /**
     * @var integer
     */
    protected $_code;
    /**
     * @var string
     */
    protected $_body;
    /**
     * @var string
     */
    protected $_location;
    /**
     * @var string
     */
    protected $_encoding = 'UTF-8';

    /**
     * @param string $body
     * @param integer $code
     */
    public function __construct($body, $code, $location = null, $encoding = null)
    {
        $this->_body = $body;
        $this->_code = $code;
        $this->_location = $location;
        if ($encoding !== null) {
            $this->_encoding = $encoding;
        }
    }

    /**
     * @return int
     */
    public function getCode()
    {
        return $this->_code;
    }

    /**
     * @return int
     */
    public function getLocation()
    {
        return $this->_location;
    }

    /**
     * @return string
     */
    public function getBody()
    {
        return $this->_body;
    }

    /**
     * @return mixed
     */
    public function __toString()
    {
        $body = json_decode($this->_body);
        if (stripos($this->_encoding, 'UTF-8') === false) {
            $body = $this->_iconvRecursive($body);
        }
        return $body;
    }

    protected function _iconvRecursive($data)
    {
        foreach ($data as $field => &$val) {
            if (is_object($val) || is_array($val)) {
                $val = $this->_iconvRecursive($val);
            } elseif (is_string($val)) {
                $val = iconv('UTF-8', $this->_encoding, $val);
            }
        }
        return $data;
    }
}