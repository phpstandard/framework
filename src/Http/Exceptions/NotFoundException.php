<?php

namespace Framework\Http\Exceptions;

use Framework\Http\StatusCodes;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Throwable;

class NotFoundException extends RuntimeException
{
    /**
     * Server request which is not dispatched
     *
     * @var ServerRequestInterface
     */
    private $request;

    public function __construct(
        ServerRequestInterface $request,
        ?string $message = 'Route not found',
        ?Throwable $previous = null
    ) {
        $this->request = $request;

        parent::__construct($message, StatusCodes::HTTP_NOT_FOUND, $previous)
    }

    /**
     * Get server reqeust
     *
     * @return ServerRequestInterface|null
     */
    public function getRequest(): ?ServerRequestInterface
    {
        return $this->request;
    }

    /**
     * Set server request
     *
     * @param ServerRequestInterface|null $request
     * @return self
     */
    public function setRequest(?ServerRequestInterface $request): self
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString()
    {
        $req = $this->getRequest();
        return $req->getMethod() . " " . $req->getUri() . " is not dispatched.";
    }
}
