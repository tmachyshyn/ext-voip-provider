<?php

namespace Espo\Modules\MyModuleName\Providers\NewProvider;

use Espo\Core\Exceptions\{
    Forbidden,
    BadRequest,
};

class WebhookHandler extends \Espo\Modules\Voip\Bases\WebhookHandler
{
    public function run(array $data, $request)
    {
        $connectorManager = $this->getConnectorManager();

        $requestType = $request->getQueryParam('type');

        switch ($requestType) {
            case 'event':
                $connectorManager->handleEvent($data);
                break;

            default:
                throw new BadRequest('Unknown request type.');
                break;
        }

        $this->printJson([
            'success' => true,
        ]);
    }
}
