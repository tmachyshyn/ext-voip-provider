<?php

namespace Espo\Modules\MyModuleName\Providers\NewProvider\Webhook\Actions;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Modules\Voip\Core\Webhook\Processor;
use Espo\Modules\Voip\Core\Event\Params as EventParams;
use Espo\Modules\Voip\Core\Webhook\Util as WebhookUtil;
use Espo\Modules\Voip\Core\Event\Factory as EventFactory;

/**
 * Processing call-related webhooks (e.g., call status changes)
 * Expected to be triggered by a webhook endpoint defined in the provider's connector configuration
 */
class Call implements Processor
{
    public function __construct(
        private EventFactory $eventFactory
    ) {}

    public function process(Request $request): Response
    {
        $connectorId = WebhookUtil::getConnectorId($request);

        $this->eventFactory
            ->create($connectorId)
            ->process(EventParams::fromRaw([
                'connectorId' => $connectorId,
                'data' => $request->getParsedBody(),
                'action' => $request->getParsedBody()->action ?? null,
            ]));

        return ResponseComposer::json(true);
    }


}
