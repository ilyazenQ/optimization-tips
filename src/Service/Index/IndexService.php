<?php 

namespace App\Service\Index;

use App\Entity\Place;
use App\Entity\User;
use Closure;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

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
    
    public function getIndexList(Request $request): array { 
        $q = $this->em
            ->getRepository(Place::class)
            ->createQueryBuilder('p')
            ->select('u','c','p')
            ->leftJoin('p.users', 'u')
            ->leftJoin('p.category', 'c')
            ->orderBy('p.title', 'ASC');
        
        // Пагинация смещением
        if((null !== (int)$request->get('limit')) && (null !== (int)$request->get('offset'))) {
            $q->setFirstResult((int)$request->get('offset'));
            $q->setMaxResults((int)$request->get('limit'));
        };

        //Курсорная пагинация
        if((null !== (int)$request->get('limit')) && (null !== $request->get('cursor_title'))) {
            $q->where('p.title > :cursor');
            $q->setParameter('cursor', $request->get('cursor_title'));
            $q->setMaxResults((int)$request->get('limit'));
        };

        return $q->getQuery()->getResult();
    }

}