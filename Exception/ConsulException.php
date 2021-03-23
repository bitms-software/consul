<?php


namespace Bitms\Component\Consul\Exception;


use Throwable;

class ConsulException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}