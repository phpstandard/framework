<?php

declare(strict_types=1);

namespace Framework\Container\Exceptions;

use Exception;
use Psr\Container\NotFoundExceptionInterface;
use Throwable;

/** @package Framework\Container\Exceptions */
class NotFoundException extends Exception implements NotFoundExceptionInterface
{
    /**
     * @param string $id 
     * @param int $code 
     * @param null|Throwable $previous 
     * @return void 
     */
    public function __construct(
        string $id,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $message = "An entry with an id of {$id} is not registered";
        parent::__construct($message, $code, $previous);
    }
}
