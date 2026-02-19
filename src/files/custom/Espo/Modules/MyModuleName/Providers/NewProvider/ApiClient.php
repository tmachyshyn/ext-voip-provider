<?php

namespace Espo\Modules\MyModuleName\Providers\NewProvider;

use Espo\Core\Exceptions\Error;
use Espo\Modules\Voip\Core\ApiClient\Client;
use stdClass;

/**
 * API client for NewProvider.
 */
class ApiClient implements Client
{
    public function __construct(
        private string $connectorId,
        private stdClass $options
    ) {}

    public function testConnection(stdClass $options): bool
    {
        // Your implementation to test the connection
        // If the connection is successful, return true. Otherwise, throw an Error
        // throw new Error();
    }

    public function dial(string $fromNumber, string $toNumber): ?string
    {
        // Your implementation to make a call using the provider's API
        // If the call is successfully initiated, return the call ID. Otherwise, return null
        // return null;
    }

    public function sendMessage(
        string $fromNumber,
        string $toNumber,
        string $text
    ): ?string {
        // Your implementation to send a message using the provider's API
        // If the message is successfully sent, return the message ID. Otherwise, return null
        // return null;
    }
}
