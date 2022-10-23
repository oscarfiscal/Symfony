<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\Product;
use App\Form\ProductType;
use Doctrine\Persistence\ManagerRegistry as PersistenceManagerRegistry;

use App\Repository\ProductRepository;


class ProductController extends AbstractController
{
    #[Route('/product', name: 'product')]
    public function index(ProductRepository $product): Response
    {
      
       $listProduct = $product->findAll();
        return $this->render('product/index.html.twig', [
           'list' => $listProduct,
        ]);
    }

    #[Route('/create', name: 'create')]

    public function createProduct(Request $request, PersistenceManagerRegistry $doctrine)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('notice', 'Producto creado correctamente');
            return $this->redirectToRoute('product');
        }
        return $this->render('product/create.html.twig', [
            'form' => $form->createView(),
        ]);
    
    }

    #[Route('/update/{id}', name: 'product_update')]
    public function editProduct(Request $request, PersistenceManagerRegistry $doctrine, $id)
    {
       
    }

    #[Route('/delete/{id}', name: 'product_delete')]
    public function deleteProduct(Request $request, PersistenceManagerRegistry $doctrine, $id)
    {
       
    }
}
