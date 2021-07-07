<?php

namespace Espo\Modules\MyModuleName\Providers\NewProvider;

class Manager extends \Espo\Modules\Voip\Bases\Manager
{
    protected function normalizeOptions(array $options, $connector = null)
    {
        return [
            'serverUrl' => $options['serverUrl'],
            'apiUser' => $options['apiUser'],
            'apiSecret' => $options['apiSecret'],
        ];
    }

    /**
     * Handle event (incoming / outgoing call)
     */
    public function handleEvent(?array $eventData = null)
    {
        $this->getEventListener()->handle($eventData);
    }

    /**
     * Test connection
     */
    public function testConnection(array $options)
    {
        $apiClient = $this->createApiClient(
            $this->normalizeOptions($options)
        );

        return $apiClient->testConnection();
    }

    /**
     * Click-to-call action
     */
    public function dial(array $data)
    {
        return $this->getApiClient()->actionDial($data['callerId'], $data['toPhoneNumber']);
    }
}
