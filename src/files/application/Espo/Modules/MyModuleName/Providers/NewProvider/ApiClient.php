<?php

namespace Espo\Modules\MyModuleName\Providers\NewProvider;

use Espo\Core\Exceptions\Error;
use Espo\Modules\Voip\Core\Utils\Voip as VoipUtils;

class ApiClient
{
    /* API client of your VoIP server / service */

    protected $serverUrl;

    protected $apiUser;

    protected $apiSecret;

    public function __construct(array $options)
    {
        $this->checkOptions($options);

        $this->serverUrl = VoipUtils::normalizerUrl($options['serverUrl'], true);
        $this->apiUser = $options['apiUser'];
        $this->apiSecret = $options['apiSecret'];
    }

    protected function checkOptions(array $options)
    {
        if (empty($options['serverUrl'])) {
            throw new Error('Empty "serverUrl" option.');
        }

        if (empty($options['apiUser'])) {
            throw new Error('Empty "apiUser" option.');
        }

        if (empty($options['apiSecret'])) {
            throw new Error('Empty "apiSecret" option.');
        }
    }

    protected function sendAction($actionName, array $data = [])
    {
        $result = VoipUtils::sendRemoteRequest('GET', $this->serverUrl, array_merge($data, [
            'username' => $this->apiUser,
            'password' => $this->apiSecret,
            'action' => $actionName,
        ]));

        if (isset($result['success']) && $result['success'] == true) {
            return true; //throw Exception on false
        }

        $errorMessage = $result['errorMessage'] ?? null;
        throw new Error($errorMessage);
    }

    public function testConnection()
    {
        return $this->sendAction('testConnection');
    }

    public function actionDial($internalUser, $toNumber)
    {
        return $this->sendAction('dial', [
            'number' => $toNumber,
            'internal' => $internalUser,
        ]);
    }
}
