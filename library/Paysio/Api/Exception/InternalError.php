<?php

namespace Paysio\Api\Exception;

class InternalError extends \RuntimeException
{
    protected $type;
    protected $code = 500;

    public function __construct($message, $type)
    {
        $this->message = $message;
        $this->type = $type;
    }

    public function getType()
    {
        return $this->type;
    }
}