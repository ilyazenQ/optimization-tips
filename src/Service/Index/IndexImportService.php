<?php 

namespace App\Service\Index;

use App\DTO\CategoryDTO;
use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class IndexImportService {

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly IndexService $indexService,
        private readonly IndexClearTableService $clearTableService
    ) {

    }

    public function importFromXml(mixed $file): void {
        $xmlData = simplexml_load_file($file);
        $json = json_encode($xmlData);
        $array = json_decode($json, true);
        $this->proccessFromArrayToEntity($array['item']);
    }   

    private function proccessFromArrayToEntity(array $array):void  {
        $this->clearTableService->clearAllIndexTable();

        foreach ($array as $item) {
            $categoryDTO = new CategoryDTO($item['category']);
            $category = $this->em
            ->getRepository(Category::class)
            ->firstOrCreateBy(['title'=>$categoryDTO->title], $categoryDTO);
            $this->em->flush();
        }

    }

}