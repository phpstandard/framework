<?php

declare(strict_types=1);

namespace Framework\Container\Exceptions;

use Exception;
use Psr\Container\ContainerExceptionInterface;
use Throwable;

class ContainerException extends Exception implements
    ContainerExceptionInterface
{
}
