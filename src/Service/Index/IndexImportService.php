<?php 

namespace App\Service\Index;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class IndexImportService {

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly IndexService $indexService,
        private readonly SerializerInterface $serializer
    ) {

    }

    public function importFromXml(mixed $file): void {
        $xmlData = simplexml_load_file($file);
        $json = json_encode($xmlData);
        $array = json_decode($json, true);
        $this->proccessFromArrayToEntity($array['item']);
    }   

    private function proccessFromArrayToEntity(array $array):void  {
        
        foreach($array as $item) {
            dd($item);
        }

    }

}