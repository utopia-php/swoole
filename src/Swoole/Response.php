<?php

namespace Utopia\Swoole;

use Utopia\Response as UtopiaResponse;
use Swoole\Http\Response as SwooleResponse;

class Response extends UtopiaResponse
{
    /**
     * Swoole Response Object
     * 
     * @var SwooleResponse
     */
    protected $swoole;
    
    /**
     * Response constructor.
     */
    public function __construct(SwooleResponse $response)
    {        
        $this->swoole = $response;
        parent::__construct(\microtime(true));
    }

    /**
     * Write
     * 
     * @param string $content
     * 
     * @return void
     */
    protected function write(string $content): void
    {
        $this->swoole->write($content);
    }

    /**
     * End
     * 
     * @param string $content
     * 
     * @return void
     */
    protected function end(string $content=null): void
    {
        $this->swoole->end($content);
    }

    /**
     * Send Status Code
     * 
     * @param int $statusCode
     * 
     * @return void
     */
    protected function sendStatus($statusCode): void
    {
        $this->swoole->status($statusCode);
    }

    /**
     * Send Header
     * 
     * @param string $key
     * @param string $value
     * 
     * @return void
     */
    protected function sendHeader(string $key, string $value): void
    {
        $this->swoole->header($key, $value);
    }

    /**
     * Send Cookie
     *
     * Send a cookie
     * 
     * @param string $name
     * @param string $value
     * @param array $options
     *
     * @return void
     */
    protected function sendCookie(string $name, string $value, array $options): void
    {
        $this->swoole->cookie(
            $name,
            $value,
            $options['expire'] ?? 0,
            $options['path'] ?? "",
            $options['domain'] ?? "",
            $options['secure'] ?? false,
            $options['httponly'] ?? false,
            $options['samesite'] ?? false,
        );

    }
}
