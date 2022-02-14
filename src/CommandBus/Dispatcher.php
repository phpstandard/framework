<?php

namespace Framework\CommandBus;

use Framework\CommandBus\Exceptions\CommandNotDispatchedException;

/** @package Framework\CommandBus */
class Dispatcher
{
    /** @var Mapper */
    private $mapper;

    /**
     * @param Mapper $mapper 
     * @return void 
     */
    public function __construct(Mapper $mapper)
    {
        $this->mapper = $mapper;
    }

    /**
     * @param object $cmd 
     * @return mixed 
     * @throws CommandNotDispatchedException 
     */
    public function dispatch(object $cmd)
    {
        $handler = $this->mapper->getHandler($cmd);
        return $handler->handle($cmd);
    }
}
