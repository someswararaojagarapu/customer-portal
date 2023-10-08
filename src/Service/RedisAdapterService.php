<?php

namespace App\CustomerPortal\Service;

use Predis\Connection\ConnectionException;
use Symfony\Component\Cache\Adapter\RedisAdapter;

final class RedisAdapterService
{
    public function __construct(private readonly string $redisUrl)
    {
    }

    public function getRedisConnection(): \Redis|\Predis\Client
    {
        try {
            // pass a single DSN string to register a single server with the client
            return RedisAdapter::createConnection(
                $this->redisUrl
            );
        } catch (ConnectionException $exception) {
            throw new \Exception("There is an issue with Redis connection. Please contact your administrator.");
        }
    }
}
