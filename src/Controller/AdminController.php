<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Form\ProductSearchType;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin")
 * @IsGranted("ROLE_ADMIN")
 */
class AdminController extends AbstractController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @IsGranted("ROLE_ADMIN")
    * @Route("/", name="admin_panel")
    */
    public function dashboard(): Response
    {
    // Verifică dacă utilizatorul are rolul "ROLE_ADMIN"
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $products = $this->productRepository->findAll();

        return $this->render('adminPanel/adminPanel.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/category/view/{category}", name="admin_category")
     */
    public function viewAdmin(ProductRepository $productRepository,Category $category, Request $request): Response
    {
        $form = $this->createForm(ProductSearchType::class);

        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $qb = $productRepository->createQueryBuilder('p')
                ->innerJoin('p.vendor', 'v')
                ->innerJoin('p.category', 'c');


            if(count($data['price_range']) > 0) {
                foreach ($data['price_range'] as $key => $range) {
                    $values = explode('-', $range);
                    $qb->orWhere("p.price BETWEEN :start$key AND :end$key")
                        ->setParameter("start$key", $values[0])
                        ->setParameter("end$key", $values[1]);
                }
            }

            if(($data['categories'])->count() > 0) {
                $qb->andWhere('p.category in (:categories)')
                    ->setParameter('categories', $data['categories']);
            }

            if(($data['name'])) {
                $qb->orWhere('p.name like :namep')
                    ->setParameter('namep', '%'.$data['name'].'%');
                $qb->orWhere('v.name like :namev')
                    ->setParameter('namev', '%'.$data['name'].'%');
                $qb->orWhere('c.name like :namec')
                    ->setParameter('namec', '%'.$data['name'].'%');
            }


            if(($data['vendors'])->count() > 0) {
                $qb->andWhere('p.vendor in (:vendors)')
                    ->setParameter('vendors', $data['vendors']);
            }

            $products = $qb->getQuery()->getResult();
        } else {
            $products = $category->getProducts();
        }

        return $this->render('adminPanel/adminPanel.html.twig', [
            'products'=>$products,
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/category/create", name="admin_create")
     */
    public function create(Request $request): Response
    {
        $category = new Category();

        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);
        if($categoryForm->isSubmitted() && $categoryForm->isValid())
        {
//            $imageFile = $categoryForm->get('file')->getData();
//            $imageFile->move('/var/www/html/Bogdan/symfonyShop/public/images', $imageFile->getClientOriginalName());
//            $category->setImage($imageFile->getClientOriginalName());

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
        }

        return $this->render('category/index.html.twig', [
            'categoryForm' => $categoryForm->createView(),
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/category/edit/{category}", name="admin_edit")
     */
    public function edit(Category $category,Request $request): Response
    {
        $categoryForm = $this->createForm(CategoryType::class, $category);

        $categoryForm->handleRequest($request);
        if($categoryForm->isSubmitted() && $categoryForm->isValid())
        {
//            $imageFile = $categoryForm->get('file')->getData();
//            $imageFile->move('/var/www/html/Bogdan/symfonyShop/public/images', $imageFile->getClientOriginalName());
//            $category->setImage($imageFile->getClientOriginalName());

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
        }

        return $this->render('category/index.html.twig', [
            'categoryForm' => $categoryForm->createView(),
            'category' => $category,  // Asigurăm că categoria este disponibilă
        ]);
    }

    /**
    * @IsGranted("ROLE_ADMIN")
    * @Route("/category/delete/{category}", name="admin_delete", methods={"DELETE"})
    */
    public function delete(Category $category, Request $request): Response
    {
        // Validare CSRF
        if (!$this->isCsrfTokenValid('delete' . $category->getId(), $request->request->get('_token'))) {
        throw $this->createAccessDeniedException('Token CSRF invalid!');
        }

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();

        $this->addFlash('success', 'Categoria a fost ștearsă cu succes!');
        return $this->redirectToRoute('admin_panel');
    }

}



