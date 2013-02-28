<?php

namespace Paysio\Api;

use \Paysio\Api;

class AbstractResource
{
    const SORT_ASC = 'asc',
          SORT_DESC = 'desc';

    /**
     * @var \Paysio\Api
     */
    protected static $_api;
    /**
     * Resource url path
     * @var string
     */
    protected $_urlPath;
    /**
     * Current response
     * @var Response
     */
    protected $_response;

    /**
     * @param null $data
     * @param \Paysio\Api $api
     */
    public function __construct($data = null, Api $api = null)
    {
        if ($data !== null) {
            $this->populate($data);
        }
        static::$_api = $api;
    }

    /**
     * @static
     * @return \Paysio\Api
     */
    protected static function _getApi()
    {
        if (static::$_api === null) {
            static::$_api = Api::getInstance();
        }
        return static::$_api;
    }

    /**
     * @return string
     */
    protected function _getUrlPath()
    {
        if ($this->_urlPath === null) {
            $class = explode('\\', get_class($this));
            $url = strtolower(end($class));
            $this->_urlPath = substr($url, -1) == 'y' ? substr($url, -1) . 'ies' : $url . 's';
        }
        return $this->_urlPath;
    }

    /**
     * @param $id
     * @return AbstractResource
     */
    protected function _fetch($id)
    {
        $url = $this->_getUrlPath() . '/' . $id;
        $response = $this->_getApi()->getClient()->get($url);
        return $this->populate($this->_handleResponse($response));
    }

    /**
     * @param array $params
     * @param null $sort
     * @param null $count
     * @param null $offset
     * @return Resource\Collection
     */
    protected function _fetchAll(array $params = null, $sort = null, $count = null, $offset = null)
    {
        if ($count !== null) {
            $params['count'] = $count;
        }
        if ($offset !== null) {
            $params['offset'] = $offset;
        }
        if ($sort !== null) {
            list($sortKey, $sortDir) = each($sort);
            $params['sort'] = $sortKey . ($sortDir == self::SORT_DESC ? '.desc' : '');
        }
        $url = $this->_getUrlPath();
        $response = $this->_getApi()->getClient()->get($url, $params);
        $response = $this->_handleResponse($response);
        return self::createCollection($response->data, $response->count);
    }

    /**
     * @param array $params
     * @return AbstractResource
     */
    protected function _create(array $params)
    {
        $url = $this->_getUrlPath();
        $response = $this->_getApi()->getClient()->post($url, $params);
        return $this->populate($this->_handleResponse($response));
    }

    /**
     * @param $id
     * @param array $params
     * @return AbstractResource
     */
    protected function _update($id, array $params)
    {
        $url = $this->_getUrlPath() . '/' . $id;
        $response = $this->_getApi()->getClient()->put($url, $params);
        return $this->populate($this->_handleResponse($response));
    }

    /**
     * @param $id
     * @return AbstractResource
     */
    protected function _delete($id)
    {
        $url = $this->_getUrlPath() . '/' . $id;
        $response = $this->_getApi()->getClient()->delete($url);
        return $this->populate($this->_handleResponse($response));
    }

    /**
     * @param Response $response
     * @return mixed
     * @throws Exception\NotFound
     * @throws Exception\BadRequest
     * @throws Exception\InternalError
     * @throws Exception\Forbidden
     * @throws Exception\Unauthorized
     */
    protected function _handleResponse(Response $response)
    {
        $this->_response = $response;
        $body = $response->__toString();
        switch ($response->getCode()) {
            case 400:
                throw new Exception\BadRequest($body->error->message, $body->error->type, isset($body->error->params) ? $body->error->params : null);
                break;
            case 401:
                throw new Exception\Unauthorized($body->error->message, $body->error->type);
                break;
            case 403:
                throw new Exception\Forbidden($body->error->message, $body->error->type);
                break;
            case 404:
                throw new Exception\NotFound($body->error->message, $body->error->type);
                break;
            case 500:
            case 503:
                throw new Exception\InternalError($body->error->message, $body->error->type);
                break;
        }

        if (is_object($body) && isset($body->object)) {
            unset($body->object);
        }

        return $body;
    }

    /**
     * @param array|\stdClass $object
     * @return AbstractResource
     */
    public function populate($object)
    {
        foreach ($object as $field => $value) {
            if (is_object($value) && isset($value->object)) {
                $objectClass = '\\Paysio\\' . ucfirst($value->object);
                unset($value->object);
                $this->$field = new $objectClass($value);
            } else {
                $this->$field = $value;
            }
        }
        return $this;
    }

    /**
     * @return AbstractResource
     * @throws \RuntimeException
     */
    public function save()
    {
        $params = $this->toArray();
        if (isset($this->id)) {
            unset($params['id']);
            $this->_update($this->id, $params);
        } else {
            $this->_create($params);
        }
        return $this;
    }

    /**
     * @return AbstractResource
     * @throws \RuntimeException
     * @throws \BadMethodCallException
     */
    public function delete()
    {
        if (!isset($this->id)) {
            throw new \BadMethodCallException('Resource ID not set');
        }
        $this->_delete($this->id);
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return (array)$this;
    }

    /**
     * @param array $params
     * @return AbstractResource
     */
    public static function create(array $params)
    {
        $resource = static::createObject();
        return $resource->_create($params);
    }

    /**
     * @static
     * @param array|\stdClass $data
     * @return AbstractResource
     */
    public static function createObject($data = null)
    {
        $resource = new static($data, static::_getApi());
        return $resource;
    }

    /**
     * @static
     * @param $id
     * @return AbstractResource
     */
    public static function retrieve($id)
    {
        $resource = static::createObject();
        return $resource->_fetch($id);
    }

    /**
     * @static
     * @param array $params
     * @return Resource\Collection
     */
    public static function all(array $params = null)
    {
        $sort = isset($params['sort']) ? $params['sort'] : null;
        $count = isset($params['count']) ? $params['count'] : null;
        $offset = isset($params['offset']) ? $params['offset'] : null;

        $resource = static::createObject();
        return $resource->_fetchAll($params, $sort, $count, $offset);
    }

    /**
     * @static
     * @param array $data
     * @param null $count
     * @return Resource\Collection
     */
    public static function createCollection(array $data, $count = null)
    {
        return new Resource\Collection($data, static::createObject(), $count);
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        return $this->_response;
    }

    /**
     * Redirect by response location
     */
    public function redirect()
    {
        if ($this->_response && $this->_response->getCode() == 201) {
            header('Location: ' . $this->getResponse()->getLocation());
        }
    }
}