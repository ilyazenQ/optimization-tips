<?php

namespace App\Controller;

use App\Service\Index\IndexService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/index')]
class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index_index', methods: ['GET'])]
    public function index(IndexService $service): Response
    {        
        return $this->render('index/index.html.twig', [
            'data' => array_map($service->getTransformerForIndex(), $service->getIndexList()),
        ]);
    }
}
