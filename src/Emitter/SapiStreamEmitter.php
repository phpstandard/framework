<?php

namespace Framework\Emitter;

use Framework\Contracts\Emitter\EmitterInterface;
use Framework\Emitter\Exceptions\EmitterException;
use Framework\Emitter\Traits\SapiEmitterTrait;
use Psr\Http\Message\ResponseInterface;

use function connection_status;
use function substr;
use function preg_match;
use const CONNECTION_NORMAL;

class SapiStreamEmitter implements EmitterInterface
{
    use SapiEmitterTrait;

    /** @var int $maxBufferSize Max size of the buffer size. Positive integer */
    private $maxBufferSize;

    public function __construct(int $max_buffer_size = 8192)
    {
        $this->setMaxBufferSize($max_buffer_size);
    }

    /**
     * Get the value of max buffer size
     * 
     * @return integer
     */
    public function getMaxBufferSize(): int
    {
        return $this->maxBufferSize;
    }

    /**
     * Set the value of max buffer size
     *
     * @return  self
     */
    public function setMaxBufferSize(int $max_buffer_size): self
    {
        if (!$max_buffer_size < 1) {
            throw new EmitterException('Buffer size must be positive integer');
        }

        $this->maxBufferSize = $max_buffer_size;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function emit(ResponseInterface $response): void
    {
        $this->assertNoPreviousOutput();

        $this->emitStatusLine($response);
        $this->emitHeaders($response);
        $this->emitStream($response);
    }

    /**
     * Emit response body as a stream
     *
     * @param ResponseInterface $response
     * @return void
     */
    private function emitStream(ResponseInterface $response): void
    {
        $range = $this->getContentRange($response);

        if ($range && $range->getUnit() == 'bytes') {
            $this->emitBodyRange($response, $range);
        } else {
            $this->emitBody($response);
        }
    }

    /**
     * Emit the response body by max buffer size
     *
     * @param ResponseInterface $response
     */
    private function emitBody(ResponseInterface $response): void
    {
        $body = $response->getBody();

        if ($body->isSeekable()) {
            $body->rewind();
        }

        if (!$body->isReadable()) {
            echo $body;
            return;
        }

        while (!$body->eof()) {
            echo $body->read($this->getMaxBufferSize());

            if (connection_status() != CONNECTION_NORMAL) {
                // Connection is broken
                // Stop emitting the rest of the stream
                break;
            }
        }
    }

    /**
     * Emit the range of the response body by max buffer size
     *
     * @param ResponseInterface $response
     * @param ContentRange $range
     * @return void
     */
    private function emitBodyRange(
        ResponseInterface $response,
        ContentRange $range
    ): void {
        $start = $range->getStart();
        $end = $range->getEnd();

        $body = $response->getBody();
        $length = $end - $start + 1;

        if ($body->isSeekable()) {
            $body->seek($start);

            $start = 0; //?
        }

        if (!$body->isReadable()) {
            echo substr($body->getContents(), $start, $length);
            return;
        }

        $remaining = $length;

        while ($remaining > 0 && !$body->eof()) {
            $contents = $body->read(
                $remaining >= $this->getMaxBufferSize()
                    ? $this->getMaxBufferSize()
                    : $remaining
            );

            echo $contents;

            if (connection_status() != CONNECTION_NORMAL) {
                // Connection is broken
                // Stop emitting the rest of the stream
                break;
            }

            $remaining -= strlen($contents);
        }
    }

    /**
     * Get ContentRange 
     * 
     * Parses the Content-Range header line from the response and generates 
     * ContentRange instance.
     * 
     * @see http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html#sec14.16
     *
     * @param ResponseInterface $response
     * @return ContentRange|null
     */
    private function getContentRange(ResponseInterface $response): ?ContentRange
    {
        $header_line = $response->getHeaderLine('Content-Range');

        if (
            !$header_line
            || !preg_match(
                '/(?P<unit>[\w]+)\s+(?P<start>\d+)-(?P<end>\d+)\/(?P<size>\d+|\*)/',
                $header_line,
                $matches
            )
        ) {
            return null;
        }

        return new ContentRange(
            $matches['start'],
            $matches['end'],
            $matches['size'] === '*' ? null : (int) $matches['size'],
            $matches['unit']
        );
    }
}
