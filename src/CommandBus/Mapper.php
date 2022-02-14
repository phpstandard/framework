<?php

namespace Framework\CommandBus;

use Framework\CommandBus\Exceptions\CommandNotDispatchedException;
use InvalidArgumentException;
use Psr\Container\ContainerInterface;

/** @package Framework\CommandBus */
class Mapper
{
    /** @var ContainerInterface */
    private $container;

    /** @var array */
    private $map = [];

    /**
     * @param ContainerInterface $container 
     * @return void 
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param object $cmd 
     * @return object 
     * @throws CommandNotDispatchedException 
     */
    public function getHandler(object $cmd): object
    {
        foreach ($this->map as $command => $handler) {
            if ($cmd instanceof $command) {
                if (!is_object($handler)) {
                    $handler = $this->container->get($handler);
                    $this->map[$command] = $handler;
                    return $handler;
                }
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
    public function map(string $cmd, $handler): self
    {
        if (!is_string($handler) && !is_object($handler)) {
            throw new InvalidArgumentException;
        }

        $this->map[$cmd] = $handler;
        return $this;
    }
}
