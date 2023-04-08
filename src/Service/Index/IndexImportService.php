<?php 

namespace App\Service\Index;

use App\DTO\CategoryDTO;
use App\DTO\PlaceDTO;
use App\DTO\UserDTO;
use App\Entity\Category;
use App\Entity\Place;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

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
            ->firstOrCreateBy(['title'=>$categoryDTO->title], $categoryDTO, true);

            $placeDTO = new PlaceDTO($item['title']);
            $place = $this->em
            ->getRepository(Place::class)
            ->firstOrCreateBy(['title'=>$placeDTO->title], $placeDTO);
            $place->setCategory($category);

            $users = explode('| ', $item['users']); 
            foreach ($users as $userStr) { 
                if($userStr === '') continue;
                $userDTO = new UserDTO($userStr, true);
                $user = $this->em
                ->getRepository(User::class)
                ->firstOrCreateBy(['name'=>$userDTO->name], $userDTO, true);

                $place->addUser($user);
            }
            $this->em->flush();

        }

    }

}