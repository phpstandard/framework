<?php

namespace Framework\Emitter\Exceptions;

use Throwable;

use function sprintf;

class HeadersAlreadySentException extends EmitterException
{
    /**
     * PHP source file name where output started in.
     *
     * @var string
     */
    private $file;

    /**
     * Line number in the PHP source file name where output started in
     *
     * @var string
     */
    private $line;

    public function __construct(
        string $file,
        ?string $line,
        int $code = 0,
        ?Throwable $previous = null
    ) {
        $msg = sprintf('Headers already sent in file %s on line %s.', $file, $line);
        parent::__construct($msg, $code, $previous);
    }

    /**
     * Get pHP source file name where output started in.
     *
     * @return  string
     */
    public function getHeadersSentFile()
    {
        return $this->file;
    }

    /**
     * Get line number in the PHP source file name where output started in
     *
     * @return  string
     */
    public function getHeadersSentLine()
    {
        return $this->line;
    }
}
