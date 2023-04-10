<?php 

namespace App\Service\Index;

use App\Entity\Place;
use App\Entity\User;
use Closure;
use Doctrine\ORM\EntityManagerInterface;

class IndexService {

    public function __construct(
        private readonly EntityManagerInterface $em,
    ) {

    }

    public function getTransformerForIndex(): Closure {

        $transformer  = function (Place $place) {
            return [
                'title' => $place->getTitle(),
                'category'  => $place->getCategory()->getTitle(),
                'users' => $place->getUsers()->reduce(fn($item,User $user) => 
                    $item .= $user->getName() . '| '
                )
            ];
        };

        return $transformer;
    }
    
    public function getIndexList(): array { 
        return $this->em
            ->getRepository(Place::class)
            ->createQueryBuilder('p')
            ->leftJoin('p.users', 'u')
            ->leftJoin('p.category', 'c')
            ->orderBy('p.title', 'ASC')
            ->getQuery()
            ->getResult();
    }

}