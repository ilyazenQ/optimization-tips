<?php

namespace App\Controller;

use App\Entity\Category;
use App\Service\Index\IndexImportService;
use App\Service\Index\IndexService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\Index\IndexClearTableService;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

#[Route('/index')]
class IndexController extends AbstractController
{
    #[Route('/', name: 'app_index_index', methods: ['GET'])]
    public function index(IndexService $service, Request $request): Response
    {        
        return $this->render('index/index.html.twig', [
            'data' => array_map($service->getTransformerForIndex(), $service->getIndexList($request)),
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
            // Обработка файла
            $indexImportService->importFromXml($file);

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

    #[Route('/test', name: 'app_index_test', methods: ['GET'])]
    public function test(IndexService $service, IndexClearTableService $clearTableService, LoggerInterface $logger, EntityManagerInterface $em): Response
    {        
        $clearTableService->clearAllIndexTable();
        
//Создаем новый обьект
//$category = new Category();
//$category->setTitle('Тестовая категория 1');
// Экземпляр новой сущности не имеет постоянной идентичности и еще не связан с EntityManager и UnitOfWork 
//dump('IdentityMap до persist', $em->getUnitOfWork()->getIdentityMap());
// Обьъект попадает в UnitOfWork (IdentityMap и ScheduledEntityInsertions)
//$em->persist($category);
//dump('IdentityMap после persist', $em->getUnitOfWork()->getIdentityMap());
//dump('ScheduledEntityInsertions после persist', $em->getUnitOfWork()->getScheduledEntityInsertions());
// Объект сохраняется в бд, удаляется из ScheduledEntityInsertions
//$em->flush();
//dump('IdentityMap после flush', $em->getUnitOfWork()->getIdentityMap());
//dump('ScheduledEntityInsertions после flush', $em->getUnitOfWork()->getScheduledEntityInsertions());

// Создаем новые категории 
//$cat2 = new Category();
//$cat2->setTitle('Тестовая категория 2');
//$cat3 = new Category();
//$cat3->setTitle('Тестовая категория 3');
//$em->persist($cat2);
//$em->flush();
//$em->persist($cat3);
//$em->flush();

// Создаем сущности
dump('Потребление памяти в начале скрипта', memory_get_usage());
for ($i=0; $i < 10000; $i++) { 
    $newCategory = new Category();
    $newCategory->setTitle($i . 'title');
    $em->persist($newCategory);
}
$em->flush();
$em->clear();
dump('UnitOfWork после создания категорий', $em->getUnitOfWork()->size());
// ... Иная логика
$someCat = new Category();
$someCat->setTitle('Тестовая категория 2');
$em->persist($someCat);
$em->flush();
dump('UnitOfWork в конце работы скрипта', $em->getUnitOfWork()->size());
dump('Потребление памяти в конце скрипта', memory_get_usage());




// Повторно запрашиваем сущность методом find
//$category2 = $em->getRepository(Category::class)
//->find(5);
//dump('IdentityMap после запросов', $em->getUnitOfWork()->getIdentityMap());
//dump('$category1 === $category2', $category1 === $category2);



        $form = $this->createFormBuilder()
        ->add('file', FileType::class)
        ->add('submit', SubmitType::class, ['label' => 'Upload'])
        ->getForm();
    
        return $this->render('index/uploadXML.html.twig', [
            'form' => $form->createView(),
            'message' => ''
        ]); 


    }
}
