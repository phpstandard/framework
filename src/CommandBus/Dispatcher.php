<?php

namespace Framework\CommandBus;

use Framework\CommandBus\Exceptions\CommandNotDispatchedException;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Container\ContainerExceptionInterface;

/** @package Framework\CommandBus */
class Dispatcher
{
    /**
     * @param Mapper $mapper
     * @return void
     */
    public function __construct(private Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param object $cmd
     * @return mixed
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws CommandNotDispatchedException
     */
    public function dispatch(object $cmd): mixed
    {
        $handler = $this->mapper->getHandler($cmd);
        return $handler->handle($cmd);
    }
}
