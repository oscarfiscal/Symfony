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

//phpOffice
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;



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

    #[Route('/excel', name: 'excel')]
    public function getExcel(ProductRepository $product){

        $listProduct = $product->findAll();
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setCellValue('A1', 'Codigo');
        $sheet->setCellValue('B1', 'Nombre del producto');
        $sheet->setCellValue('C1', 'Descripcion');
        $sheet->setCellValue('D1', 'Marca');
        $sheet->setCellValue('E1', 'Precio');
        $sheet->setCellValue('F1', 'Fecha de creacion');
        $sheet->setCellValue('G1', 'Categoria');
       

        $style=[
            'font' => [
                'bold' => true,
                'size' => 12,
                'color' => ['argb' => 'FFFFFF'],
                'name' => 'Arial'
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'color' => ['argb' => '000000'],
            ],
        ];

       
        $sheet->getStyle('A1:G1')->getFill()->setFillType(Fill::FILL_SOLID)->getStartColor()->setARGB('000000');
        $sheet->getStyle('A1:G1')->getFont()->getColor()->setARGB(\PhpOffice\PhpSpreadsheet\Style\Color::COLOR_WHITE);

        for ($i = 0; $i < count($listProduct); $i++) {
            $counter = $i + 2;
            $sheet->setCellValue("A" .  $counter, $listProduct[$i]->getCode());
            $sheet->getStyle("A" . $counter)->getFill()->setFillType(Fill::FILL_SOLID);
            $sheet->getStyle("A" . $counter)->getFill()->getStartColor()->setRGB("dc2780");

            $sheet->setCellValue("B" . $counter, $listProduct[$i]->getName());
                 
            $sheet->setCellValue("C" . $counter, $listProduct[$i]->getDescription());

            $sheet->setCellValue("D" . $counter, $listProduct[$i]->getBrand());

            $sheet->setCellValue("E" . $counter, $listProduct[$i]->getPrice());

            $sheet->setCellValue("F" . $counter, $listProduct[$i]->getCreatedAt());

            $sheet->setCellValue("G" . $counter, $listProduct[$i]->getCategory());

        }

        $sheet->getStyle('A1:G1')->applyFromArray($style);

        $sheet->setTitle('Productos');
        //column dimension
        $sheet->getColumnDimension('A')->setWidth(20);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(20);
        $sheet->getColumnDimension('D')->setWidth(20);
        $sheet->getColumnDimension('E')->setWidth(20);
        $sheet->getColumnDimension('F')->setWidth(20);
        $sheet->getColumnDimension('G')->setWidth(20);


        $writer = new Xlsx($spreadsheet);

        // file name and temporal file
        $actualDate = (new \DateTime())->format('Y-m-d');
        $fileName= 'Productos-'.$actualDate.'.xlsx';
        $temp_file = tempnam(sys_get_temp_dir(), $fileName);

        $writer->save($temp_file);

        return $this->file($temp_file, $fileName, ResponseHeaderBag::DISPOSITION_INLINE);

    }
}
