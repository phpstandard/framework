<?php

declare(strict_types=1);

namespace Framework\Emitter;

use Framework\Contracts\Emitter\EmitterInterface;
use Framework\Emitter\Traits\SapiEmitterTrait;
use Psr\Http\Message\ResponseInterface;

/** @package Framework\Emitter */
class SapiEmitter implements EmitterInterface
{
    use SapiEmitterTrait;

    /**
     * @inheritDoc
     */
    public function emit(ResponseInterface $response): void
    {
        $this->assertNoPreviousOutput();
        $this->emitStatusLine($response);
        $this->emitHeaders($response);
        $this->emitBody($response);
    }

    /**
     * Emit the response body
     *
     * @param ResponseInterface $response
     */
    private function emitBody(ResponseInterface $response): void
    {
        echo $response->getBody();
    }
}
