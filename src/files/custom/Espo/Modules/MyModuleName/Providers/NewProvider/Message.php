<?php

namespace Espo\Modules\MyModuleName\Providers\NewProvider;

use Espo\Core\Exceptions\BadRequest;
use Espo\Modules\Voip\Core\Message\Params;
use Espo\Modules\Voip\Core\Message\IParams;
use Espo\Modules\Voip\Entities\VoipMessage;
use Espo\Modules\Voip\Core\Message\Processor;
use Espo\Modules\Voip\Core\Message\Action\Factory as ActionFactory;

class Message implements Processor
{
    public function __construct(
        private ActionFactory $actionFactory
    ) {}

    public function process(IParams $params): VoipMessage
    {
        /** @var Params $params */

        $data = $params->getData();
        $connectorId = $params->getConnectorId();

        $action = $data->action ?? null;

        if (!$action) {
            throw new BadRequest('Incorrect data.');
        }

        return $this->actionFactory
            ->create($connectorId, $action)
            ->process($params);
    }
}
