<?php 

namespace App\Service\Index;

use App\DTO\CategoryDTO;
use App\DTO\PlaceDTO;
use App\DTO\UserDTO;
use App\Entity\Category;
use App\Entity\Place;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use SimpleXMLElement;

class IndexImportService {

    public function __construct(
        private readonly EntityManagerInterface $em,
        private readonly IndexService $indexService,
        private readonly IndexClearTableService $clearTableService,
        private readonly LoggerInterface $logger,
    ) {

    }

    public function importFromXml(mixed $file):void {
        // Truncate таблиц Category, User, Place и их связей
        $this->clearTableService->clearAllIndexTable();
        // Получение содержимого файла
        $xmlData = simplexml_load_file($file);
        // Преобразование в содержимого в массивы
        $categoriesFromFile = array_unique($xmlData->xpath('//category'));
        $usersFromFile = array_unique($xmlData->xpath('//users'));
        $placesFromFile = $this->fromXmlToArr($xmlData);
        // Обрабатываем информацию, заполняем базу данных
        $this->processCategory($categoriesFromFile);
        $this->processUser($this->getUsersFromFile($usersFromFile));
        $this->processPlace($placesFromFile);
    }

    private function processCategory(array $categories):void {

        foreach($categories as $cat) {
            // Заполняем DTO
            $categoryDTO = new CategoryDTO($cat->__toString());
            // Создаем новый объект из DTO, делаем persist
            $category = new Category();
            $category->setTitle($categoryDTO->title);
            $this->em->persist($category);
            // Синхронизируем изменения с бд
            $this->em->flush(); 
        }

    }

    private function processUser(array $users):void {

        foreach($users as $user) {
            // Заполняем DTO
            $userDTO = new UserDTO($user,true);
            // Создаем новый объект из DTO, делаем persist
            $newUser = new User();
            $newUser->setName($userDTO->name);
            $newUser->setIsActive($userDTO->isActive);
            $this->em->persist($newUser);
            // Синхронизируем изменения с бд
            $this->em->flush(); 
        }

    }

    private function processPlace(array $array):void {
        foreach ($array as $item) {
            // Поиск связанной категории по title
            $categoryDTO = new CategoryDTO($item['category']);
            $category = $this->em
            ->getRepository(Category::class)
            ->findOneBy(['title'=>$categoryDTO->title]);
            // Создаем новый объект из DTO
            $placeDTO = new PlaceDTO($item['title']);
            $place = new Place();
            $place->setTitle($placeDTO->title);
            $place->setCategory($category);
            // Поиск пользователей
            $users = $this->getUsersFromItem($item['users']);
            foreach ($users as $user) { 
                $user = $this->em
                ->getRepository(User::class)
                ->findOneBy(['name'=>$user]);

                $place->addUser($user);
            }
            $this->em->persist($place);
            // Синхронизируем изменения с бд
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