<?php

namespace Espo\Modules\MyModuleName\Providers\NewProvider\Actions;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Modules\Voip\Core\Action\Processor;
use Espo\Modules\Voip\Tools\Dial\Tool as DialTool;
use Espo\Modules\Voip\Core\ApiClient\Factory as ApiClientFactory;
use Espo\Modules\MyModuleName\Providers\NewProvider\ApiClient;

class Dial implements Processor
{
    public function __construct(
        private DialTool $dialTool,
        private ApiClientFactory $apiClientFactory
    ) {}

    public function process(Request $request): Response
    {
        $data = $this->dialTool->normalizeDataByRequest($request);

        $connectorId = $data->getConnectorId();

        $callerId = $data->getCallerId();
        $phoneNumber = $data->getPhoneNumber();

        /** @var ApiClient $apiClient */
        $apiClient = $this->apiClientFactory->create($connectorId);

        $id = $apiClient->dial($callerId, $phoneNumber);

        return ResponseComposer::json([
            'id' => $id,
        ]);
    }
}
