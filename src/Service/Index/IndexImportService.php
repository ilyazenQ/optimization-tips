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
        dump('Потребление памяти до обработки', memory_get_usage());
        $this->processCategory($categoriesFromFile);
        dump('Потребление памяти после обработки категорий', memory_get_usage());
        dump('UnitOfWork после обработки категорий', $this->em->getUnitOfWork()->size());
        $this->processUser($this->getUsersFromFile($usersFromFile));
        dump('Потребление памяти после обработки пользователей', memory_get_usage());
        dump('UnitOfWork после обработки пользователей', $this->em->getUnitOfWork()->size());
        $this->processPlace($placesFromFile);
        dump('UnitOfWork в конце работы скрипта', $this->em->getUnitOfWork()->size());
        dump('Потребление памяти в конце работы скрипта', memory_get_usage());

    }

    private function processCategory(array $categories):void {
        // Количество записей, которые будут вставлены за один запрос
        $count     = 1;
        $batchSize = 250;
        foreach($categories as $cat) {
            // Заполняем DTO
            $categoryDTO = new CategoryDTO($cat->__toString());
            // Создаем новый объект из DTO, делаем persist
            $category = new Category();
            $category->setTitle($categoryDTO->title);
            $this->em->persist($category);

            if ($count % $batchSize === 0) {
                $this->em->flush();
                // очистить EntityManager для уменьшения использования памяти
                $this->em->clear();
                $count = 1;
            } else {
                $count++;
            }
        }
           $this->em->flush();
           $this->em->clear();
    }

    private function processUser(array $users):void {
        // Количество записей, которые будут вставлены за один запрос
        $count     = 1;
        $batchSize = 250;
        foreach($users as $user) {
            // Заполняем DTO
            $userDTO = new UserDTO($user,true);
            // Создаем новый объект из DTO, делаем persist
            $newUser = new User();
            $newUser->setName($userDTO->name);
            $newUser->setIsActive($userDTO->isActive);
            $this->em->persist($newUser);
            if ($count % $batchSize === 0) {
                $this->em->flush();
                // очистить EntityManager для уменьшения использования памяти
                $this->em->clear();
                $count = 1;
            } else {
                $count++;
            }
        }
           $this->em->flush();
           $this->em->clear();
    }

    private function processPlace(array $array):void {
        // Количество записей, которые будут вставлены за один запрос
        $count     = 1;
        $batchSize = 250;
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
            if ($count % $batchSize === 0) {
                $this->em->flush();
                // очистить EntityManager для уменьшения использования памяти
                $this->em->clear();
                $count = 1;
            } else {
                $count++;
            }
        }
           $this->em->flush();
           $this->em->clear();
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