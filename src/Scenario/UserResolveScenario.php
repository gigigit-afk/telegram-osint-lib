<?php

declare(strict_types=1);

namespace TelegramOSINT\Scenario;

use TelegramOSINT\Exception\TGException;
use TelegramOSINT\Logger\Logger;
use TelegramOSINT\MTSerialization\AnonymousMessage;

class UserResolveScenario extends InfoClientScenario
{
    /** @var string */
    private $username;
    /** @var int|null */
    private $userId;
    /** @var callable */
    private $cb;

    /**
     * @param string                        $username
     * @param callable                      $cb              function(int|null $userId)
     * @param ClientGeneratorInterface|null $clientGenerator
     */
    public function __construct(string $username, callable $cb, ClientGeneratorInterface $clientGenerator = null)
    {
        parent::__construct($clientGenerator);
        $this->username = $username;
        $this->cb = $cb;
    }

    /**
     * @param bool $pollAndTerminate
     *
     * @throws TGException
     */
    public function startActions(bool $pollAndTerminate = true): void
    {
        $this->infoClient->resolveUsername($this->username, $this->getUserResolveHandler($this->cb));

        if ($pollAndTerminate) {
            $this->pollAndTerminate();
        }
    }

    /**
     * @param callable $cb function()
     *
     * @return callable function(AnonymousMessage $msg)
     */
    private function getUserResolveHandler(callable $cb): callable
    {
        return function (AnonymousMessage $message) use ($cb) {
            if ($message->getType() === 'contacts.resolvedPeer' && $message->getValue('users')) {
                $user = $message->getValue('users')[0];
                if ($user['_'] == 'user') {
                    $this->userId = (int) $user['id'];
                    Logger::log(__CLASS__, "resolved user {$this->username} to {$this->userId}");
                    $cb($this->userId);

                    return;
                }
            }
            $cb(null);
        };
    }
}