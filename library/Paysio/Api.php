<?php

namespace Paysio;

class Api
{
    /**
     * @var string
     */
    protected static $_key;
    /**
     * @var string
     */
    protected static $_publishableKey;
    /**
     * @var string
     */
    protected static $_url = 'https://api.paysio.com';
    /**
     * @var string
     */
    protected static $_version = '1';
    /**
     * @var string
     */
    protected static $_encoding = 'UTF-8';
    /**
     * @var string
     */
    protected static $_currency = Currency::TYPE_RUB;

    /**
     * @var Api
     */
    protected static $_instance;

    /**
     * @var Api\Client
     */
    protected $_client;
    /**
     * @var string
     */
    protected $_clientClass = '\Paysio\Api\Client';

    /**
     * @param $key
     * @param array $options
     * @throws \InvalidArgumentException
     */
    public function __construct($key, array $options = array())
    {
        if (!$key) {
            throw new \InvalidArgumentException('API-key must be defined');
        }
        self::$_key = $key;
        if (!empty($options['url'])) {
            self::$_url = $options['url'];
        }
        if (!empty($options['version'])) {
            self::$_version = $options['version'];
        }
        if (!empty($options['encoding'])) {
            self::$_encoding = $options['encoding'];
        }
        if (!empty($options['currency'])) {
            self::$_currency = $options['currency'];
        }
        if (!empty($options['publishable_key'])) {
            self::$_publishableKey = $options['publishable_key'];
        }
    }

    /**
     * @return Api\Client
     */
    public function getClient()
    {
        if ($this->_client === null) {
            $this->_client = new $this->_clientClass($this);
        }
        return $this->_client;
    }

    /**
     * @static
     * @return Api
     * @throws \InvalidArgumentException
     */
    public static function getInstance()
    {
        if (static::$_instance === null) {
            static::$_instance = new static(static::$_key);
        }
        return static::$_instance;
    }

    /**
     * @static
     * @param $value
     */
    public static function setKey($value)
    {
        static::$_key = $value;
    }

    /**
     * @static
     * @param $value
     */
    public static function setPublishableKey($value)
    {
        static::$_publishableKey = $value;
    }

    /**
     * @static
     * @param $value
     */
    public static function setUrl($value)
    {
        static::$_url = $value;
    }

    /**
     * @static
     * @param $value
     */
    public static function setVersion($value)
    {
        static::$_version = $value;
    }

    /**
     * @static
     * @param $value
     */
    public static function setEncoding($value)
    {
        static::$_encoding = $value;
    }

    /**
     * @static
     * @param $value
     */
    public static function setCurrency($value)
    {
        static::$_currency = $value;
    }

    /**
     * @static
     * @return string
     */
    public static function getCurrency()
    {
        return static::$_currency;
    }

    /**
     * @static
     * @return string
     */
    public static function getEndpoint()
    {
        return static::getUrl() . '/v' . static::getVersion();
    }

    /**
     * @static
     * @return string
     */
    public static function getVersion()
    {
        return static::$_version;
    }

    /**
     * @static
     * @return string
     */
    public static function getUrl()
    {
        return static::$_url;
    }

    /**
     * @static
     * @return string
     */
    public static function getKey()
    {
        return static::$_key;
    }

    /**
     * @static
     * @return string
     */
    public static function getPublishableKey()
    {
        return static::$_publishableKey;
    }

    /**
     * @static
     * @return string
     */
    public static function getEncoding()
    {
        return static::$_encoding;
    }

    /**
     * @static
     * @return string
     */
    public static function getStaticUrl()
    {
        return str_replace('api.', '', static::getUrl()) . '/static/v' . static::getVersion();
    }
}
