<?php

namespace Espo\Modules\MyModuleName\Providers\NewProvider\Actions;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Exceptions\Error;
use Espo\Core\Api\ResponseComposer;
use Espo\Modules\Voip\Core\Action\Processor;
use Espo\Modules\MyModuleName\Providers\NewProvider\ApiClient;
use Espo\Modules\Voip\Core\ApiClient\Factory as ApiClientFactory;

class TestConnection implements Processor
{
    public function __construct(
        private ApiClientFactory $apiClientFactory
    ) {}

    public function process(Request $request): Response
    {
        $data = $request->getParsedBody();
        $connectorId = $data->connectorId ?? null;

        if (empty($connectorId)) {
            throw new Error('Connector ID is required.');
        }

        if (empty($data->host)) {
            throw new Error('Host is required.');
        }

        if (empty($data->username) || empty($data->secret)) {
            throw new Error('Username and secret are required.');
        }

        /** @var ApiClient $apiClient */
        $apiClient = $this->apiClientFactory->create($connectorId);

        return ResponseComposer::json(
            $apiClient->testConnection($data)
        );
    }
}
