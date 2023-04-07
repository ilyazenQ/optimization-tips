<?php

namespace App\Controller;

use App\Service\Index\IndexImportService;
use App\Service\Index\IndexService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
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

    #[Route('/upload', name: 'app_index_uploadForm', methods: ['GET', 'POST'])]
    public function uploadXmlForm(Request $request, IndexImportService $indexImportService): Response
    {    
        $form = $this->createFormBuilder()
        ->add('file', FileType::class)
        ->add('submit', SubmitType::class, ['label' => 'Upload'])
        ->getForm();
    
    $form->handleRequest($request);
    
    if ($form->isSubmitted() && $form->isValid()) {
        $file = $form->get('file')->getData();
        
        $indexImportService->importFromXml($file);
        // Обработка файла
        
        return $this->render('index/uploadXML.html.twig', [
            'form' => $form->createView(),
            'message' => 'Upload completed'
        ]); 
    }
    
    return $this->render('index/uploadXML.html.twig', [
        'form' => $form->createView(),
        'message' => ''
    ]);    
    
    }
}
