<?php

namespace Utopia\Swoole\Dispatch;

use Swoole\Server;

interface DispatchInterface
{
    /**
     * Connection dispatch type
     *
     * @link https://www.swoole.co.uk/docs/modules/swoole-server/configuration#dispatch_func
     */
    public const CONNECTION_FETCH  = 10;
    public const CONNECTION_START  = 5;
    public const CONNECTION_CLOSE  = 4;

    /**
     * Resolve requests to corresponding worker processes
     *
     * @param Server $server
     * @param int $fd Client ID number
     * @param int $type Dispatch type
     * @param ?string $data Request packet data (0-8180 bytes)
     *
     * @return int Worker ID number
     */
    public function __invoke(Server $server, int $fd, int $type, ?string $data = null): int;
}
