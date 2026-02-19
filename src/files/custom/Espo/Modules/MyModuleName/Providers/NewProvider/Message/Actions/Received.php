<?php

namespace Espo\Modules\MyModuleName\Providers\NewProvider\Message\Actions;

use Espo\ORM\EntityManager;
use Espo\Modules\Voip\Core\Message\Params;
use Espo\Modules\Voip\Core\Message\IParams;
use Espo\Modules\Voip\Entities\VoipMessage;
use Espo\Modules\Voip\Core\Message\Processor;

/**
 * This class processes incoming VoIP messages (e.g., SMS) from the provider.
 * It extracts necessary information from the message data, creates a new VoipMessage entity.
 */
class Received implements Processor
{
    public function __construct(
        private EntityManager $entityManager
    ) {}

    public function process(IParams $params): VoipMessage
    {
        /** @var Params $params */

        $data = $params->getData();
        $connectorId = $params->getConnectorId();

        /** @var VoipMessage $message */
        $message = $this->entityManager->createEntity(VoipMessage::ENTITY_TYPE, [
            'connector' => $connectorId,
            'type' => VoipMessage::TYPE_SMS,
            'direction' => VoipMessage::DIRECTION_INCOMING,
            'status' => VoipMessage::STATUS_RECEIVED,
            'body' => $data->body ?? null,
            'externalId' => $data->messageId ?? null,
            'from' => $data->from ?? null,
            'to' => $data->to ?? null,
            'dateSent' => date('Y-m-d H:i:s'),
        ]);

        return $message;
    }
}
