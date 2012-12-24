<?php

namespace Paysio\Api\Exception;

class Unauthorized extends \RuntimeException
{
    protected $type;
    protected $code = 401;

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