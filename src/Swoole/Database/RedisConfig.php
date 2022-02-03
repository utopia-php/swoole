<?php

namespace Utopia\Swoole\Database;

use Swoole\Database\RedisConfig as SwooleRedisConfig;

class RedisConfig extends SwooleRedisConfig
{
    /** @var string|array */
    protected $auth = '';

    /**
     * @param string|array $auth
     */
    public function withAuth($auth): self
    {
        $this->auth = $auth;
        return $this;
    }
}
