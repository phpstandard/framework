<?php

namespace Framework\Emitter\Exceptions;

use Throwable;

use function sprintf;

/** @package Framework\Emitter\Exceptions */
class HeadersAlreadySentException extends EmitterException
{
    /**
     * @param string $file PHP source file name where output started in
     * @param null|string $line Line number in the PHP source file name where 
     * output started in
     * @param int $code 
     * @param null|Throwable $previous 
     * @return void 
     */
    public function __construct(
        private string $file,
        private ?string $line,
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
    public function getHeadersSentFile(): string
    {
        return $this->file;
    }

    /**
     * Get line number in the PHP source file name where output started in
     *
     * @return  string
     */
    public function getHeadersSentLine(): string
    {
        return $this->line;
    }
}
