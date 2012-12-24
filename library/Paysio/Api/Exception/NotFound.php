<?php

namespace Paysio\Api\Exception;

class NotFound extends \RuntimeException
{
    protected $type;
    protected $code = 404;

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