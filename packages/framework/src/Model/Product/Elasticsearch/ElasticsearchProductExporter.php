<?php

namespace Shopsys\FrameworkBundle\Model\Product\Elasticsearch;

use Elasticsearch\Client;

class ElasticsearchProductExporter
{
    const BATCH_SIZE = 100;

    /**
     * @var \Elasticsearch\Client
     */
    protected $client;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Elasticsearch\ElasticsearchProductRepository
     */
    protected $repository;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Product\Elasticsearch\ElasticsearchProductTranslator
     */
    protected $translator;

    public function __construct(
        Client $client,
        ElasticsearchProductRepository $repository,
        ElasticsearchProductTranslator $translator
    ) {
        $this->client = $client;
        $this->repository = $repository;
        $this->translator = $translator;
    }

    /**
     * @param int $domainId
     * @param string $locale
     */
    public function export(int $domainId, string $locale): void
    {
        $first = 0;
        $exportedIds = [];
        do {
            $batchExportedIds = $this->exportBatch($domainId, $locale, $first);
            $exportedIds = array_merge($exportedIds, $batchExportedIds);
            $first += self::BATCH_SIZE;
        } while (!empty($batchExportedIds));
        $this->removeNotExported((string)$domainId, $exportedIds);
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @param int $first
     * @return int[]
     */
    protected function exportBatch(int $domainId, string $locale, int $first): array
    {
        $productsData = $this->repository->getProductsData($domainId, $locale, $first, self::BATCH_SIZE);
        if (count($productsData) === 0) {
            return [];
        }

        $data = $this->translator->translateBulk((string)$domainId, $productsData);
        $params = [
            'body' => $data,
        ];
        $this->client->bulk($params);

        return $this->translator->extractIds($productsData);
    }

    /**
     * @param string $index
     * @param int[] $exportedIds
     */
    protected function removeNotExported(string $index, array $exportedIds): void
    {
        $this->client->deleteByQuery([
            'index' => $index,
            'type' => '_doc',
            'body' => [
                'query' => [
                    'bool' => [
                        'must_not' => [
                            'ids' => [
                                'values' => $exportedIds,
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }
}
