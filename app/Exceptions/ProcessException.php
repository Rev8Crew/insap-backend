<?php

namespace App\Exceptions;

use Exception;
use Throwable;

class ProcessException extends Exception
{
    private string $processError;

    public function __construct($message = "", $code = 0, Throwable $previous = null, string $processError = '')
    {
        parent::__construct($message, $code, $previous);

        $this->processError = $processError;
    }

    /**
     * @return string
     */
    public function getProcessError(): string
    {
        return $this->processError;
    }
}
