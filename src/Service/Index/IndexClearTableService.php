<?php 

namespace App\Service\Index;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\SerializerInterface;

class IndexClearTableService {

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {

    }

    public function clearTable(string $table): void {
        $connection = $this->em->getConnection();
        $platform = $connection->getDatabasePlatform();
        $connection->executeStatement($platform->getTruncateTableSQL($table, true));
    }

    public function clearAllIndexTable(): void {
        $tables = ['Place', 'Category', 'user', 'place_user'];

        foreach($tables as $table) {
            $this->clearTable($table);
        }
    }


}