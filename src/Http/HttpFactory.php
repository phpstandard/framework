<?php

/**
 * This factory class internally uses Laminas Diactoros library.
 * 
 * @see https://github.com/laminas/laminas-diactoros
 */

declare(strict_types=1);

namespace Framework\Http;

use Laminas\Diactoros\RequestFactory;
use Laminas\Diactoros\ResponseFactory;
use Laminas\Diactoros\ServerRequestFactory;
use Laminas\Diactoros\StreamFactory;
use Laminas\Diactoros\UploadedFileFactory;
use Laminas\Diactoros\UriFactory;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestFactoryInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\UploadedFileFactoryInterface;
use Psr\Http\Message\UploadedFileInterface;
use Psr\Http\Message\UriFactoryInterface;
use Psr\Http\Message\UriInterface;

class HttpFactory implements
    RequestFactoryInterface,
    ResponseFactoryInterface,
    ServerRequestFactoryInterface,
    StreamFactoryInterface,
    UploadedFileFactoryInterface,
    UriFactoryInterface
{
    /** @var RequestFactoryInterface $requestFactory */
    private $requestFactory;

    /** @var ResponseFactoryInterface $responseFactory */
    private $responseFactory;

    /** @var ServerRequestFactoryInterface $serverRequestFactory */
    private $serverRequestFactory;

    /** @var StreamFactoryInterface $streamFactory */
    private $streamFactory;

    /** @var UploadedFileFactoryInterface $uploadedFileFactory */
    private $uploadedFileFactory;

    /** @var UriFactoryInterface $uriFactory */
    private $uriFactory;

    /**
     * @inheritDoc
     */
    public function createRequest(string $method, $uri): RequestInterface
    {
        if (!$this->requestFactory) {
            $this->requestFactory = new RequestFactory;
        }

        return $this->requestFactory->createRequest($method, $uri);
    }

    /**
     * @inheritDoc
     */
    public function createResponse(
        int $code = 200,
        string $reasonPhrase = ''
    ): ResponseInterface {
        if (!$this->responseFactory) {
            $this->responseFactory = new ResponseFactory;
        }

        return $this->responseFactory->createResponse($code, $reasonPhrase);
    }

    /**
     * @inheritDoc
     */
    public function createServerRequest(
        string $method,
        $uri,
        array $serverParams = []
    ): ServerRequestInterface {
        if (!$this->serverRequestFactory) {
            $this->serverRequestFactory = new ServerRequestFactory;
        }

        return $this->serverRequestFactory
            ->createServerRequest($method, $uri, $serverParams);
    }

    /**
     * @inheritDoc
     */
    public function createStream(string $content = ''): StreamInterface
    {
        if (!$this->streamFactory) {
            $this->streamFactory = new StreamFactory;
        }

        return $this->streamFactory->createStream($content);
    }

    /**
     * @inheritDoc
     */
    public function createStreamFromFile(string $filename, string $mode = 'r'): StreamInterface
    {
        if (!$this->streamFactory) {
            $this->streamFactory = new StreamFactory;
        }

        return $this->streamFactory->createStreamFromFile($filename, $mode);
    }

    /**
     * @inheritDoc
     */
    public function createStreamFromResource($resource): StreamInterface
    {
        if (!$this->streamFactory) {
            $this->streamFactory = new StreamFactory;
        }

        return $this->streamFactory->createStreamFromResource($resource);
    }

    /**
     * @inheritDoc
     */
    public function createUploadedFile(
        StreamInterface $stream,
        ?int $size = null,
        int $error = \UPLOAD_ERR_OK,
        ?string $clientFilename = null,
        ?string $clientMediaType = null
    ): UploadedFileInterface {
        if (!$this->uploadedFileFactory) {
            $this->uploadedFileFactory = new UploadedFileFactory;
        }

        return $this->uploadedFileFactory->createUploadedFile(
            $stream,
            $size,
            $error,
            $clientFilename,
            $clientMediaType
        );
    }

    /**
     * @inheritDoc
     */
    public function createUri(string $uri = ''): UriInterface
    {
        if (!$this->uriFactory) {
            $this->uriFactory = new UriFactory;
        }

        return $this->uriFactory->createUri($uri);
    }

    /**
     * Create a server request from the PHP Superglobals
     *
     * @return ServerRequestInterface
     */
    public static function getGlobalServerRequest(): ServerRequestInterface
    {
        return ServerRequestFactory::fromGlobals();
    }
}
