<?php

namespace Espo\Modules\MyModuleName\Providers\NewProvider\Event\Actions;

use Espo\ORM\EntityManager;
use Espo\Modules\Voip\Core\Event\Params;
use Espo\Modules\Voip\Core\Event\IParams;
use Espo\Modules\Voip\Entities\VoipEvent;
use Espo\Modules\Voip\Core\Event\Processor;
use Espo\Modules\Voip\Tools\VoipEvent\Creator as VoipEventCreator;

/**
 * This class processes status update events for existing VoIP calls.
 * It updates the status of the corresponding VoipEvent entity in the database
 * based on the external ID (callId) provided in the event data.
 */
class StatusUpdated implements Processor
{
    public function __construct(
        private EntityManager $entityManager,
        private VoipEventCreator $voipEventCreator
    ) {}

    public function process(IParams $params): ?VoipEvent
    {
        /** @var Params $params */

        $data = $params->getData();
        $connectorId = $params->getConnectorId();

        $externalId = $data->callId ?? null;
        $status = $data->status ?? null;

        $recordingUrl = $data->recordingUrl ?? null;

        if (!$externalId || !$status) {
            return null;
        }

        $voipEvent = $this->voipEventCreator->create($connectorId, $externalId);

        if (!$voipEvent) {
            return null;
        }

        $voipEvent->set('status', $status);

        if ($recordingUrl) {
            $voipEvent->addCustomDataItem('recordingUrl', $recordingUrl);
        }

        $this->entityManager->saveEntity($voipEvent);

        return $voipEvent;
    }
}
