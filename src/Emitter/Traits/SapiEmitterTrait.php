<?php

namespace Framework\Emitter\Traits;

use Framework\Emitter\Exceptions\HeadersAlreadySentException;
use Framework\Emitter\Exceptions\PreviousOutputException;
use Psr\Http\Message\ResponseInterface;

use function headers_sent;
use function ob_get_level;
use function ob_get_length;
use function sprintf;
use function header;
use function ucwords;
use function strtolower;

trait SapiEmitterTrait
{
    /**
     * Assert either that no headers been sent or the output buffer contains no
     * content.
     *
     * @return void
     */
    private function assertNoPreviousOutput(): void
    {
        $file = null;
        $line = null;

        if (headers_sent($file, $line)) {
            throw new HeadersAlreadySentException($file, $line);
        }

        if (ob_get_level() > 0 && ob_get_length() > 0) {
            throw new PreviousOutputException();
        }
    }

    /**
     * Emit the status line.
     *
     * Emits the status line using the protocol version and status code from
     * the response; if a reason phrase is available, it, too, is emitted.
     *
     * @param ResponseInterface $response
     * @return void
     */
    private function emitStatusLine(ResponseInterface $response): void
    {
        $reasonPhrase = $response->getReasonPhrase();
        $statusCode = $response->getStatusCode();
        $protocolVersion = $response->getProtocolVersion();

        $header = sprintf(
            'HTTP/%s %d%s',
            $protocolVersion,
            $statusCode,
            ($reasonPhrase ? ' ' . $reasonPhrase : '')
        );

        header($header, true, $statusCode);
    }

    /**
     * Emit response headers.
     *
     * Loops through each header, emitting each; if the header value
     * is an array with multiple values, ensures that each is sent
     * in such a way as to create aggregate headers (instead of replace
     * the previous).
     *
     * @param ResponseInterface $response
     * @return void
     */
    private function emitHeaders(ResponseInterface $response): void
    {
        $statusCode = $response->getStatusCode();

        foreach ($response->getHeaders() as $name => $values) {
            $name  = $this->normalizeHeaderName($name);

            // Replace previous headers of the same type which
            // set out of this method. Never replace cookie headers.
            $replace = $name !== 'Set-Cookie';
            foreach ($values as $value) {
                $header = sprintf('%s: %s', $name, $value);
                header($header, $replace, $statusCode);

                $replace = false;
            }
        }
    }

    /**
     * Normalize the a header name
     *
     * Normalized header will be in the following format: Example-Header-Name
     *
     * @param string $headerName
     * @return string
     */
    private function normalizeHeaderName(string $headerName): string
    {
        return ucwords(strtolower($headerName), '-');
    }
}
