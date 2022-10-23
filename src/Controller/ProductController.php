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
// Include paginator interface
use Knp\Component\Pager\PaginatorInterface;


class ProductController extends AbstractController
{
    #[Route('/product', name: 'product')]
    
    public function index(ProductRepository $product, PaginatorInterface $paginator, Request $request): Response
    {
      
        $query = $product->findAllProducts();

        $listProducts = $paginator->paginate(
            $query, /* query NOT result */
            $request->query->getInt('page', 1), /*page number*/
            5 /*limit per page*/
        );

        return $this->render('product/index.html.twig', [
           'listProducts' => $listProducts,
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
    public function editProduct(Request $request, PersistenceManagerRegistry $doctrine,ProductRepository $product, $id)
    {
        $product = $product->find($id);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $product = $form->getData();
            $entityManager = $doctrine->getManager();
            $entityManager->persist($product);
            $entityManager->flush();
            $this->addFlash('notice', 'Producto actualizado correctamente');
            return $this->redirectToRoute('product');
        }
        return $this->render('product/update.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'product_delete')]

    public function deleteProduct(PersistenceManagerRegistry $doctrine, $id)
    {
        $entityManager = $doctrine->getManager();
        $product = $entityManager->getRepository(Product::class)->find($id);
        $entityManager->remove($product);
        $entityManager->flush();
        $this->addFlash('success', 'Producto eliminado correctamente');
        return $this->redirectToRoute('product');
       
    }
}
