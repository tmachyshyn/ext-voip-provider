<?php

namespace Espo\Modules\MyModuleName\Providers\NewProvider;

use Espo\Core\Api\Request;
use Espo\Core\Api\Response;
use Espo\Core\Api\ResponseComposer;
use Espo\Core\Exceptions\BadRequest;
use Espo\Modules\Voip\Core\Lookup\Processor;
use Espo\Modules\Voip\Tools\Lookup\Entity as EntityLookup;
use Espo\Modules\Voip\Tools\Lookup\Tools\Collection as CollectionLookupTool;

/**
 * Processing CID Lookup request
 */
class CidLookup implements Processor
{
    public function __construct(
        private EntityLookup $entityLookup
    ) {}

    public function process(Request $request): Response
    {
        $connectorId = $request->getRouteParam('connectorId');
        $phoneNumber = $request->getQueryParam('number');

        if (!$phoneNumber) {
            throw new BadRequest('Option "phoneNumber" is required');
        }

        $collection = $this->entityLookup->findByPhone($connectorId, $phoneNumber);

        $entities = CollectionLookupTool::getRaw($collection);

        return ResponseComposer::json($entities);
    }
}
