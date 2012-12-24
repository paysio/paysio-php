<?php

namespace Paysio\Api\Exception;

class BadRequest extends \RuntimeException
{
    protected $type;
    protected $params;
    protected $code = 400;

    public function __construct($message, $type, $params = null)
    {
        $this->message = $message;
        $this->type = $type;
        $this->params = $params;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getParams()
    {
        return $this->params;
    }
}