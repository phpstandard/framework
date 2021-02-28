<?php

declare(strict_types=1);

namespace Framework\Container\Exceptions;

use Exception;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

class NotFoundException extends Exception implements NotFoundExceptionInterface
{
    public function __construct(
        string $id,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $message = "An entry with an id of {$id} is not registered";
        parent::__construct($message, $code, $previous);
    }
}
