<?php

namespace Espo\Modules\MyModuleName\Providers\NewProvider\Webhook\Actions;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Modules\Voip\Core\Webhook\Processor;
use Espo\Modules\Voip\Core\Webhook\Util as WebhookUtil;
use Espo\Modules\Voip\Core\Message\Params as MessageParams;
use Espo\Modules\Voip\Core\Message\Factory as MessageFactory;

/**
 * Processing message-related webhooks (e.g., message received, status updates)
 * Expected to be triggered by a webhook endpoint defined in the provider's connector configuration
 */
class Message implements Processor
{
    public function __construct(
        private MessageFactory $messageFactory
    ) {}

    public function process(Request $request): Response
    {
        $connectorId = WebhookUtil::getConnectorId($request);

        $this->messageFactory
            ->create($connectorId)
            ->process(MessageParams::fromRaw([
                'connectorId' => $connectorId,
                'data' => $request->getParsedBody(),
            ]));

        return ResponseComposer::json(true);
    }
}
