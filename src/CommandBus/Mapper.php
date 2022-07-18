<?php

namespace Framework\CommandBus;

use Framework\CommandBus\Exceptions\CommandNotDispatchedException;
use InvalidArgumentException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/** @package Framework\CommandBus */
class Mapper
{
    /** @var array */
    private array $map = [];

    /**
     * @param ContainerInterface $container 
     * @return void 
     */
    public function __construct(
        private ContainerInterface $container
    ) {
    }

    /**
     * @param object $cmd 
     * @return object 
     * @throws NotFoundExceptionInterface
     * @throws ContainerExceptionInterface 
     * @throws CommandNotDispatchedException
     */
    public function getHandler(object $cmd): object
    {
        foreach ($this->map as $command => $handler) {
            if ($cmd instanceof $command) {
                if (!is_object($handler)) {
                    $handler = $this->container->get($handler);
                    $this->map[$command] = $handler;
                }

                return $handler;
            }
        }

        throw new CommandNotDispatchedException;
    }

    /**
     * @param string $cmd 
     * @param mixed $handler 
     * @return Mapper 
     * @throws InvalidArgumentException 
     */
    public function map(string $cmd, mixed $handler): self
    {
        if (!is_string($handler) && !is_object($handler)) {
            throw new InvalidArgumentException;
        }

        $this->map[$cmd] = $handler;
        return $this;
    }
}
