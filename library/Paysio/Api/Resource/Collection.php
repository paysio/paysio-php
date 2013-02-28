<?php

namespace Paysio\Api\Resource;

use \Paysio\Api\AbstractResource;

class Collection implements \IteratorAggregate, \ArrayAccess, \Countable
{
    protected $_data = array();
    protected $_totalCount = 0;

    protected $_resourceClass;

    public function __construct(array $data, AbstractResource $resource, $totalCount = null)
    {
        foreach ($data as $row) {
            $this->_data[] = $resource::createObject($row);
        }

        $this->_totalCount = $totalCount !== null ? $totalCount : $this->count();
    }

    public function count()
    {
        return count($this->_data);
    }

    public function getTotalCount()
    {
        return $this->_totalCount;
    }

    public function getIterator()
    {
        return new \ArrayIterator($this->_data);
    }

    public function offsetSet($offset, $value)
    {
        if (is_null($offset)) {
            $this->_data[] = $value;
        } else {
            $this->_data[$offset] = $value;
        }
    }

    public function offsetExists($offset)
    {
        return isset($this->_data[$offset]);
    }

    public function offsetUnset($offset)
    {
        unset($this->_data[$offset]);
    }

    public function offsetGet($offset)
    {
        return isset($this->_data[$offset]) ? $this->_data[$offset] : null;
    }
}