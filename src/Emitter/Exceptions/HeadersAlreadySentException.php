<?php

namespace Framework\Emitter\Exceptions;

use Throwable;

use function sprintf;

/** @package Framework\Emitter\Exceptions */
class HeadersAlreadySentException extends EmitterException
{
    /**
     * @param string $headersSentFile PHP source file name where output started in
     * @param null|string $headersSentLine Line number in the PHP source file name where
     * output started in
     * @param int $code
     * @param null|Throwable $previous
     * @return void
     */
    public function __construct(
        private string $headersSentFile,
        private ?string $headersSentLine,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $msg = sprintf('Headers already sent in file %s on line %s.', $headersSentFile, $headersSentLine);
        parent::__construct($msg, $code, $previous);
    }

    /**
     * Get pHP source file name where output started in.
     *
     * @return  string
     */
    public function getHeadersSentFile(): string
    {
        return $this->headersSentFile;
    }

    /**
     * Get line number in the PHP source file name where output started in
     *
     * @return  string
     */
    public function getHeadersSentLine(): string
    {
        return $this->headersSentLine;
    }
}
