<?php

namespace Espo\Modules\MyModuleName\Providers\NewProvider;

use Espo\Modules\Voip\Entities\VoipEvent;

class EventListener extends \Espo\Modules\Voip\Bases\EventListener
{
    protected $permittedEventList = [
        'incoming',
        'outgoing',
        'connected',
        'hangup',
    ];

    public function handle(array $eventData)
    {
        $state = $eventData['requestType'];

        if (!in_array($state, $this->permittedEventList)) {
            return;
        }

        $connector = $this->getConnector();

        $voipRepository = $this->getVoipEventRepository();
        $voipEvent = $voipRepository->createEvent($eventData['callId'], null, $connector);

        $isSave = false;

        switch ($state) {
            case 'incoming': /* Ringing (incoming call) */
                $phoneNumber = $eventData['fromNumber'];
                $userExtension = $eventData['toNumber'];
                $queueNumber = $eventData['queueNumber'] ?? null;

                $voipEvent = $voipRepository->createEvent($eventData['callId'], [
                    'phoneNumber' => $phoneNumber,
                    'userExtension' => $userExtension,
                    'queueNumber' => $queueNumber,
                ], $connector);

                $voipEvent->set([
                    'type' => VoipEvent::INCOMING_CALL,
                    'status' => VoipEvent::RINGING,
                    'dateStart' => date('Y-m-d H:i:s'),
                    'phoneNumber' => $phoneNumber,
                    'queueNumber' => $queueNumber,
                ]);

                if (isset($userExtension)) {
                    $voipEvent->set('userExtension', $userExtension);
                }

                $isSave = true;
                break;

            case 'outgoing': /* Ringing (outgoing call) */
                $userExtension = $eventData['fromNumber'];
                $phoneNumber = $eventData['toNumber'];
                $queueNumber = $eventData['queueNumber'] ?? null;

                $voipEvent = $voipRepository->createEvent($eventData['callId'], [
                    'userExtension' => $userExtension,
                    'phoneNumber' => $phoneNumber,
                ], $connector);

                $voipEvent->set([
                    'type' => VoipEvent::OUTGOING_CALL,
                    'status' => VoipEvent::DIALING,
                    'dateStart' => date('Y-m-d H:i:s'),
                    'userExtension' => $userExtension,
                    'phoneNumber' => $phoneNumber,
                    'queueNumber' => $queueNumber,
                    'ready' => true,
                ]);

                $isSave = true;
                break;

            case 'connected': /* call is answered */
                $voipEvent->set([
                    'status' => VoipEvent::ACTIVE,
                    'dateStart' => date('Y-m-d H:i:s'),
                    'ready' => true,
                ]);

                $isSave = true;
                break;

            case 'hangup':  /* Call is finished */
                $voipEvent->set([
                    'status' => $voipRepository->getEndedCallStatus($voipEvent->get('status')),
                ]);

                $isSave = true;
                break;
        }

        if ($isSave && $voipRepository->isNeedToSave($voipEvent)) {
            $this->getEntityManager()->saveEntity($voipEvent);
        }

        return $voipEvent;
    }
}
