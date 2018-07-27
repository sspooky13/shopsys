<?php

namespace Shopsys\FrameworkBundle\Model\Product\Elasticsearch;

class ElasticsearchProductTranslator
{
    /**
     * @param string $index
     * @param array $data
     * @return array
     */
    public function translateBulk(string $index, array $data): array
    {
        $result = [];
        foreach ($data as $row) {
            $result[] = [
                'index' => [
                    '_index' => $index,
                    '_type' => '_doc',
                    '_id' => (string)$row['id'],
                ],
            ];

            unset($row['id']);
            $result[] = $row;
        }

        return $result;
    }
}
