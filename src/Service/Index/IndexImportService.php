<?php 

namespace App\Service\Index;

use App\DTO\CategoryDTO;
use App\DTO\PlaceDTO;
use App\DTO\UserDTO;
use App\Entity\Category;
use App\Entity\Place;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;

class IndexImportService {

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly IndexService $indexService,
        private readonly IndexClearTableService $clearTableService
    ) {

    }

    public function importFromXml(mixed $file):void {
        $this->clearTableService->clearAllIndexTable();

        $xmlData = simplexml_load_file($file);
        $categoriesFromFile = array_unique($xmlData->xpath('//category'));
        $usersFromFile = array_unique($xmlData->xpath('//users'));
        $this->processCategory($categoriesFromFile);
        $this->processUser($this->getUsersFromFile($usersFromFile));
        $this->processPlace($this->fromXmlToArr($xmlData));
    }

    private function processCategory(array $categories):void {

        foreach($categories as $cat) {
            $categoryDTO = new CategoryDTO($cat->__toString());
            $this->em
            ->getRepository(Category::class)
            ->createFromDTO($categoryDTO);
            $this->em->flush(); 
        }

    }

    private function processUser(array $users):void {
        
        foreach($users as $user) {
            $userDTO = new UserDTO($user,true);
            $this->em
            ->getRepository(User::class)
            ->createFromDTO($userDTO);
            $this->em->flush(); 
        }
    }

    private function processPlace(array $array):void {

        foreach ($array as $item) {
            $categoryDTO = new CategoryDTO($item['category']);
            $category = $this->em
            ->getRepository(Category::class)
            ->findOneBy(['title'=>$categoryDTO->title]);

            $placeDTO = new PlaceDTO($item['title']);
            $place = $this->em
            ->getRepository(Place::class)
            ->createFromDTO($placeDTO);
            $place->setCategory($category);

            $users = $this->getUsersFromItem($item['users']);
            foreach ($users as $user) { 
                $user = $this->em
                ->getRepository(User::class)
                ->findOneBy(['name'=>$user]);

                $place->addUser($user);
            }

            $this->em->persist($place);
            $this->em->flush();
        }
    }

    private function fromXmlToArr(SimpleXMLElement $xmlData):array {
        $json = json_encode($xmlData);
        $array = json_decode($json, true);
        return $array['item'];
    }


    private function getUsersFromItem(string $usersItem):array {
        $usersItem = explode('| ', $usersItem); 
        $users = [];
            foreach($usersItem as $userName) {
                if($userName === '') continue;
                $users[] = $userName;
            } 
        return array_unique($users);
    }

    private function getUsersFromFile(array $usersFromFile):array {
        $users = [];
        foreach($usersFromFile as $usersItem) {
            $users = [...$users,...$this->getUsersFromItem($usersItem)];
        }
        return array_unique($users);
    }

}