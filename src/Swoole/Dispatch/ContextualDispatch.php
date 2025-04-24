<?php

namespace Utopia\Swoole\Dispatch;

use Swoole\Server;
use Swoole\Table;

/**
 * Dispatch requests to workers according to the HTTP request message
 */
abstract class ContextualDispatch implements DispatchInterface
{
    private Table $dispatchMap;

    /**
     * @param int $dispatcherMapSize This should correspond to the max_connection paramter
     *                               https://wiki.swoole.com/en/#/server/setting?id=max_conn-max_connection
     */
    public function __construct(readonly int $dispatcherMapSize)
    {
        $this->dispatchMap = new Table($dispatcherMapSize);
        $this->dispatchMap->column('workerId', Table::TYPE_INT);
        $this->dispatchMap->create();
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke(Server $server, int $fd, int $type, ?string $data = null): int
    {
        if ($this->dispatchMap->exists($fd)) {
            $workerId = $this->dispatchMap->get($fd, 'workerId');
        } else {
            $workerId = $this->resolveWorkerId($server, $data);
            $this->dispatchMap->set($fd, ['workerId' => $workerId]);
        }
        if ($type == self::CONNECTION_CLOSE) {
            $this->dispatchMap->delete($fd);
        }
        return $workerId;
    }

    /**
     * Extract request identifying information from a request message
     *
     * @param Server $server
     * @param ?string $data
     *
     * @return int
     */
    abstract protected function resolveWorkerId(Server $server, ?string $data): int;
}
