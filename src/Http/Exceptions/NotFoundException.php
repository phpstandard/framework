<?php

namespace Framework\Http\Exceptions;

use Framework\Http\StatusCodes;
use Psr\Http\Message\ServerRequestInterface;
use RuntimeException;
use Stringable;
use Throwable;

/** @package Framework\Http\Exceptions */
class NotFoundException extends RuntimeException implements Stringable
{
    /**
     * @param ServerRequestInterface $request
     *  Server request which is not dispatched
     * @param null|string $message
     * @param null|Throwable $previous
     * @return void
     */
    public function __construct(
        private ServerRequestInterface $request,
        ?string $message = 'Route not found',
        ?Throwable $previous = null
    ) {
        parent::__construct($message, StatusCodes::HTTP_NOT_FOUND, $previous);
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
    public function setRequest(
        ?ServerRequestInterface $request
    ): NotFoundException {
        $this->request = $request;
        return $this;
    }

    /**
     * @inheritDoc
     */
    public function __toString(): string
    {
        $req = $this->getRequest();
        return $req->getMethod() . " " . $req->getUri() . " is not dispatched.";
    }
}
