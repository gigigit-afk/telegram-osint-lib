<?php

/** @noinspection SpellCheckingInspection */

declare(strict_types=1);

namespace TelegramOSINT\Scenario;

use TelegramOSINT\Client\InfoObtainingClient\InfoClient;
use TelegramOSINT\Client\StatusWatcherClient\StatusWatcherCallbacks;
use TelegramOSINT\Client\StatusWatcherClient\StatusWatcherClient;
use TelegramOSINT\Exception\TGException;

class ClientGenerator implements ClientGeneratorInterface
{
    /** @var string */
    private $envName;

    public function __construct(string $envName)
    {
        $this->envName = $envName;
    }

    public function getInfoClient()
    {
        return new InfoClient();
    }

    public function getStatusWatcherClient(StatusWatcherCallbacks $callbacks)
    {
        return new StatusWatcherClient($callbacks);
    }

    /**
     * @throws TGException
     *
     * @return string
     */
    public function getAuthKey(): string
    {
        $envPath = getenv($this->envName);
        if (!$envPath || !file_exists($envPath)) {
            throw new TGException(0, "Please set {$this->envName} env var to valid filename");
        }

        return trim(file_get_contents($envPath));
    }
}
