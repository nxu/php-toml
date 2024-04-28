<?php

namespace Nxu\PhpToml\Exceptions;

use Exception;

class TomlParserException extends Exception
{
    public static function throw(string $message, int $line): void
    {
        $exception = new self($message);
        $exception->line = $line;

        throw $exception;
    }
}
