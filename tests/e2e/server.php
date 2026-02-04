<?php

require_once __DIR__.'/../../vendor/autoload.php';

use Swoole\Http\Request as SwooleRequest;
use Swoole\Http\Response as SwooleResponse;
use Swoole\Http\Server;
use Swoole\Process;
use Utopia\Http;
use Utopia\Swoole\Request;
use Utopia\Swoole\Response;

ini_set('memory_limit', '512M');
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
ini_set('display_socket_timeout', -1);
error_reporting(E_ALL);

$http = new Server('0.0.0.0', Http::getENV('PORT', '80'));

$payloadSize = max('4000000'/* 4mb */, Http::getEnv('_APP_STORAGE_LIMIT', '10000000'/* 10mb */));

$http
    ->set([
        'open_http2_protocol' => true,
        'http_compression' => true,
        'http_compression_level' => 6,
        'package_max_length' => $payloadSize,
        'buffer_output_size' => $payloadSize,
        'worker_num' => 1,
    ]);

$http->on('WorkerStart', function ($serv, $workerId) {
    echo 'Worker '.++$workerId.' started succefully';
});

$http->on('BeforeReload', function ($serv, $workerId) {
    echo 'Starting reload...';
});

$http->on('AfterReload', function ($serv, $workerId) {
    echo 'Reload completed...';
});

$http->on('start', function (Server $http) use ($payloadSize) {
    echo 'Server started succefully (max payload is '.number_format((float) $payloadSize).' bytes)';

    echo "Master pid {$http->master_pid}, manager pid {$http->manager_pid}";

    // listen ctrl + c
    Process::signal(2, function () use ($http) {
        echo 'Stop by Ctrl+C';
        $http->shutdown();
    });
});

Http::get('/')
    ->inject('response')
    ->action(function ($response) {
        $response->send('Hello World!');
    });

Http::get('/headers')
    ->inject('request')
    ->inject('response')
    ->action(function ($request, $response) {
        $response->json(['headers' => $request->getHeaders()]);
    });

Http::get('/set-cookie')
    ->inject('request')
    ->inject('response')
    ->action(function (Request $request, Response $response) {
        $response->addHeader('Set-Cookie', 'key1=value1', override: false);
        $response->addHeader('Set-Cookie', 'key2=value2', override: false);
        $response->send('OK');
    });

Http::get('/chunked')
    ->inject('response')
    ->action(function ($response) {
        // /** @var Utopia/Swoole/Response $response */
        foreach (['Hello ', 'World!'] as $key => $word) {
            $response->chunk($word, $key == 1);
        }
    });

Http::get('/redirect')
    ->inject('response')
    ->action(function ($response) {
        // /** @var Utopia/Swoole/Response $response */
        $response->redirect('/');
    });

Http::get('/protocol')
    ->inject('request')
    ->inject('response')
    ->action(function ($request, $response) {
        // /** @var Utopia/Swoole/Response $response */
        // /** @var Utopia/Swoole/Request $request */
        $response->send($request->getProtocol());
    });

Http::get('/cookie')
    ->inject('response')
    ->action(function (Response $response) {
        $response->addCookie('new-cookie', 'session-secret', \time(), '/', 'domain.com', true, true, null);
        $response->send('Hello with cookie!');
    });

$http->on('request', function (SwooleRequest $swooleRequest, SwooleResponse $swooleResponse) {
    $request = new Request($swooleRequest);
    $response = new Response($swooleResponse);

    $app = new Http('UTC');

    try {
        $app->run($request, $response);
    } catch (\Throwable$th) {
        echo '[Error] Type: '.get_class($th);
        echo '[Error] Message: '.$th->getMessage();
        echo '[Error] File: '.$th->getFile();
        echo '[Error] Line: '.$th->getLine();

        if (Http::isDevelopment()) {
            $swooleResponse->end('error: '.$th->getMessage());
        } else {
            $swooleResponse->end('500: Server Error');
        }
    }
});

$http->start();
