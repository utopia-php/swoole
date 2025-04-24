<?php

namespace Utopia\Swoole\Dispatch;

use Swoole\Server;

abstract class RiskyRequest extends ContextualDispatch
{
    /**
     * @param float $riskyWorkersPercent Decimal form 0 to 1
     */
    public function __construct(
        int $dispatcherMapSize,
        private readonly float $riskyWorkersPercent
    ) {
        parent::__construct($dispatcherMapSize);
        if ($this->riskyWorkersPercent < 0 || $this->riskyWorkersPercent > 1) {
            throw new \InvalidArgumentException('riskyWorkersPercent must be >=0 && <=1');
        }
    }

    /**
     * @param string $request
     * @param string $domain
     * @return bool
     */
    abstract protected function isRisky(string $request, string $domain): bool;

    protected function randomRiskyWorker(int $riskyWorkers, int $totalWorkers): int
    {
        return rand($riskyWorkers, $totalWorkers - 1);
    }

    protected function randomSafeWorker(int $riskyWorkers): int
    {
        return rand(0, $riskyWorkers - 1);
    }

    protected function resolveWorkerId(Server $server, ?string $data): int
    {
        $totalWorkers = $server->setting['worker_num'];

        // If data is not set, we can send the request to any worker.
        // First we try to pick an idle worker, otherwise we randomly pick a worker.
        if (empty($data)) {
            for ($i = 0; $i < $totalWorkers; $i++) {
                if ($server->getWorkerStatus($i) === SWOOLE_WORKER_IDLE) {
                    return $i;
                }
            }
            return rand(0, $totalWorkers - 1);
        }

        // Each worker has a numeric ID, starting from 0 and incrementing
        // From 0 to $riskyWorkers, we consider safe workers
        // From $riskyWorkers to $totalWorkers, we consider risky workers
        $riskyWorkers = (int) floor($totalWorkers * $this->riskyWorkersPercent); // Absolute number of risky workers

        $headers = explode("\n", strstr($data, "\r\n", true));
        $request = $headers[0];
        $domain = '';
        if (count($headers) > 1) {
            $domain = trim(explode('Host: ', $headers[1])[1]);
        }

        $risky = $this->isRisky($request, $domain);

        if ($risky) {
            // If risky request, only consider risky workers
            for ($j = $riskyWorkers; $j < $totalWorkers; $j++) {
                /** Reference https://openswoole.com/docs/modules/swoole-server-getWorkerStatus#description */
                if ($server->getWorkerStatus($j) === SWOOLE_WORKER_IDLE) {
                    // If idle worker found, give to him
                    return $j;
                }
            }

            // If no idle workers, give to random risky worker
            return $this->randomRiskyWorker($riskyWorkers, $totalWorkers);
        }

        // If safe request, give to any idle worker
        // It's fine to pick a risky worker here because it's idle. Idle is never actually risky
        for ($i = 0; $i < $totalWorkers; $i++) {
            if ($server->getWorkerStatus($i) === SWOOLE_WORKER_IDLE) {
                return $i;
            }
        }

        // If no idle worker found, give to a random safe worker
        // We avoid risky workers here, as it could be in work - not idle. That's exactly when they are risky.
        return $this->randomSafeWorker($riskyWorkers);
    }
}
