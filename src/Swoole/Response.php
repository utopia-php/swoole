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

    protected function write($content)
    {
        $this->swoole->write($content);
    }

    protected function end($content=null)
    {
        $this->swoole->end($content);
    }

    protected function sendHeader($key, $value): self
    {
        $this->swoole->header($key, $value);
        return $this;
    }

    /**
     * Append cookies
     *
     * Iterating over response cookies to generate them using native PHP cookie function.
     *
     * @return self
     */
    protected function sendCookie(array $cookie)
    {
        $this->swoole->cookie(
            $cookie['name'],
            $cookie['value'],
            $cookie['expire'],
            $cookie['path'],
            $cookie['domain'],
            $cookie['secure'],
            $cookie['httponly'],
            $cookie['samesite'],
        );

    }
}
