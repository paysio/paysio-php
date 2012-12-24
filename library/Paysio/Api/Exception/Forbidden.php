<?php

namespace Paysio\Api\Exception;

class Forbidden extends \RuntimeException
{
    protected $type;
    protected $code = 403;

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