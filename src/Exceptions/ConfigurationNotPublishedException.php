<?php

namespace Ajosav\Blinqpay\Exceptions;

use Exception;
use PHPUnit\Event\Code\Throwable;

class ConfigurationNotPublishedException extends Exception
{
    /**
     * @param string $message
     * @param ?int $status_code
     * @param ?Throwable $previous
     */
    public function __construct(string $message, ?int $status_code = 500, ?Throwable $previous = null)
    {
        parent::__construct(message: $message, code: $status_code, previous: $previous);
    }
}