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

class CategoryController extends AbstractController
{
    private ProductRepository $productRepository;

    public function __construct(ProductRepository $productRepository)
    {
        $this->productRepository = $productRepository;
    }

    /**
     * @Route("/", name="home")
     */
    public function index(): Response
    {

        $products = $this->productRepository->findAll();

        return $this->render('default/category.html.twig', [
            'products' => $products
        ]);
    }


    /**
     * @Route("/category/view/{category}", name="category")
     */
    public function viewUser(ProductRepository $productRepository,Category $category, Request $request): Response
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

        return $this->render('default/category.html.twig', [
            'products'=>$products,
            'form' => $form->createView()
        ]);
    }

}
