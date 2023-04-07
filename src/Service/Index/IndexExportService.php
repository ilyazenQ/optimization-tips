<?php 

namespace App\Service\Index;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class IndexExportService {

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly IndexService $indexService,
        private readonly SerializerInterface $serializer
    ) {

    }

    public function exportToXml(): void {
        $entities = $this->indexService->getIndexList();
        $xmlData = $this->serializeEntitiesToXml($entities);

        $filename = 'data.xml';
        file_put_contents($filename, $xmlData);
    }

    private function serializeEntitiesToXml(array $entities): string {
        $data = array_map($this->indexService->getTransformerForIndex(), $this->indexService->getIndexList());
        return $this->serializer->serialize($data, 'xml');
    }


}