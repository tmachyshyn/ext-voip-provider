<?php

namespace Espo\Modules\MyModuleName\Providers\NewProvider;

use Espo\ORM\EntityManager;
use Espo\Modules\Voip\Entities\VoipEvent;
use Espo\Modules\Voip\Core\Recording\Util;
use Espo\Modules\Voip\Core\Recording\Processor;

class Recording implements Processor
{
    public function __construct(
        private Util $util,
        private EntityManager $entityManager
    ) {}

    public function process(VoipEvent $voipEvent, string $triggeredBy): void
    {
        // For VoIP Integration v2.6 and lower:
        // $url = $voipEvent->get('data')->recordingUrl ?? null;
        $url = $voipEvent->getCustomData()->recordingUrl ?? null;

        if (!$url) {
            return;
        }

        $call = $voipEvent->getCall();

        if (!$call) {
            return;
        }

        if ($call->get(VoipEvent::CALL_FIELD_RECORDING_FILE . 'Id')) {
            return;
        }

        $this->util
            ->upload(
                $call,
                VoipEvent::CALL_FIELD_RECORDING_FILE,
                $url
            );

        $this->entityManager->saveEntity($call);
    }
}
