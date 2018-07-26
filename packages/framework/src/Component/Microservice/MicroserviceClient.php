<?php

namespace Shopsys\FrameworkBundle\Component\Microservice;

use GuzzleHttp\Client;

class MicroserviceClient
{
    /**
     * @var \GuzzleHttp\Client
     */
    private $guzzleClient;

    public function __construct()
    {
        $this->guzzleClient = new Client();
    }

    /**
     * @param int $domainId
     * @param string $query
     * @return object
     */
    public function search(int $domainId, string $query) {
        $uri = sprintf('http://microservice-product-search:8000/%s/product-ids', $domainId);
        $response = $this->guzzleClient->get($uri, ['query' => ['query' => $query]]);

        $responseContent = $response->getBody()->getContents();

        return json_decode($responseContent);
    }
}
