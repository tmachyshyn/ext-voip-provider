<?php

namespace Espo\Modules\MyModuleName\Providers\NewProvider\Message\Actions;

use Espo\Core\Utils\Log;
use Espo\ORM\EntityManager;
use Espo\Modules\Voip\Core\Message\Params;
use Espo\Modules\Voip\Core\Message\IParams;
use Espo\Modules\Voip\Entities\VoipMessage;
use Espo\Modules\Voip\Core\Message\Processor;

/**
 * This class processes status update events for existing VoIP messages.
 * It updates the status of the corresponding VoipMessage entity in the database.
 */
class StatusUpdated implements Processor
{
    public function __construct(
        private Log $log,
        private EntityManager $entityManager
    ) {}

    public function process(IParams $params): VoipMessage
    {
        /** @var Params $params */

        $data = $params->getData();

        $messageId = $data->messageId ?? null;
        $status = $data->status ?? null;

        if (!$messageId || !$status) {
            $this->log->error(
                'Message: Data is incomplete, data: ' . var_export($data, true)
            );
        }

        $voipMessage = $this->entityManager
            ->getRDBRepositoryByClass(VoipMessage::class)
            ->where([
                'externalId' => $messageId
            ])
            ->findOne();

        $voipMessage->set('status', $status);

        return $voipMessage;
    }
}
