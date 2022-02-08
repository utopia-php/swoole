<?php

namespace Utopia\Swoole\Database;

use Swoole\Database\RedisConfig as SwooleRedisConfig;

class RedisConfig extends SwooleRedisConfig
{
    /** @var array */
    protected $userauth = [];

    /**
     * @param array $auth
     */
    public function withUserAuth(array $userauth): self
    {
        $this->userauth = $userauth;
        return $this;
    }

    /**
     * @return string|array $auth
     */
    public function getUserAuth(): array
    {
        return $this->userauth;
    }
}
