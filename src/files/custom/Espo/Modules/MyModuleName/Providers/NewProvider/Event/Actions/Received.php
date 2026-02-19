<?php

namespace Espo\Modules\MyModuleName\Providers\NewProvider\Event\Actions;

use Espo\ORM\EntityManager;
use Espo\Modules\Voip\Core\Event\Params;
use Espo\Modules\Voip\Core\Event\IParams;
use Espo\Modules\Voip\Entities\VoipEvent;
use Espo\Modules\Voip\Core\Event\Processor;
use Espo\Modules\Voip\Tools\VoipEvent\Creator as VoipEventCreator;

/**
 * This class processes incoming VoIP events from the provider.
 * It extracts necessary information from the event data, creates a new VoipEvent entity,
 * and saves it to the database.
 */
class Received implements Processor
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

        // The list of types can be found at
        // https://github.com/tmachyshyn/ext-voip-provider/tree/master?tab=readme-ov-file#types
        $type = $data->type ?? null;

        // The list of statuses can be found at
        // https://github.com/tmachyshyn/ext-voip-provider/tree/master?tab=readme-ov-file#statuses
        $status = $data->status ?? null;

        $phoneNumber = $data->phoneNumber ?? null;
        $userExtension = $data->agentExtension ?? null;

        $startedAt = $data->startedAt ?? date('Y-m-d H:i:s');
        $endedAt = $data->endedAt ?? date('Y-m-d H:i:s');

        if (!$externalId || !$phoneNumber || !$userExtension) {
            return null;
        }

        $voipEvent = $this->voipEventCreator->create($connectorId, $externalId);

        $voipEvent->set([
            'type' => $type,
            'status' => $status,
            'phoneNumber' => $phoneNumber,
            'userExtension' => $userExtension,
            'dateStart' => $startedAt,
            'dateEnd' => $endedAt,
        ]);

        $this->entityManager->saveEntity($voipEvent);

        return $voipEvent;
    }
}
