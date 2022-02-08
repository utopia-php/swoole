<?php

namespace Utopia\Swoole\Database;

use Redis;
use Swoole\ConnectionPool;
use Utopia\Swoole\Database\RedisConfig;

class RedisPool extends ConnectionPool
{
    /** @var RedisConfig */
    protected $config;

    public function __construct(RedisConfig $config, int $size = self::DEFAULT_SIZE)
    {
        $this->config = $config;
        parent::__construct(function () {
            $redis = new Redis();

            $arguments = [
                $this->config->getHost(),
                $this->config->getPort(),
            ];
            if ($this->config->getTimeout() !== 0.0) {
                $arguments[] = $this->config->getTimeout();
            }
            if ($this->config->getRetryInterval() !== 0) {
                /* reserved should always be NULL */
                $arguments[] = null;
                $arguments[] = $this->config->getRetryInterval();
            }
            if ($this->config->getReadTimeout() !== 0.0) {
                $arguments[] = $this->config->getReadTimeout();
            }
            $redis->connect(...$arguments);
            if ($this->config->getAuth()) {
                $redis->auth($this->config->getAuth());
            }
            if ($this->config->getUserAuth()) {
                $redis->auth($this->config->getUserAuth());
            }
            if ($this->config->getDbIndex() !== 0) {
                $redis->select($this->config->getDbIndex());
            }
            return $redis;
        }, $size);
    }
}
