<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Controller\ProductController;
use App\Controller\SearchController;
use App\Form\ProductSearchType;
use App\Repository\VendorRepository;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DefaultController extends AbstractController
{
    /**
     * @Route("/", name="homepage")
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findAll();
        return $this->render('default/index.html.twig', ['categories' => $categories]);
    }


    /**
     * @Route("/category", name="category_search")
     */
    public function category(ProductRepository $productRepository, CategoryRepository $categoryRepository, VendorRepository $vendorRepository, Category $category, Request $request): Response
    {
        $form = $this->createForm(ProductSearchType::class);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();


            $qb = $productRepository->createQueryBuilder('p')
                ->innerJoin('p.vendor', 'v')
                ->innerJoin('p.category', 'c');

            if (count($data['price_range']) > 0) {
                foreach ($data['price_range'] as $key => $range) {
                    $values = explode('-', $range);
                    $qb->orWhere("p.price BETWEEN :start$key AND :end$key")
                        ->setParameter("start$key", $values[0])
                        ->setParameter("end$key", $values[1]);
                }
            }

            if ($data['categories']->count() > 0) {
                $qb->andWhere('p.category in (:categories)')
                    ->setParameter('categories', $data['categories']);
            }

            if ($data['name']) {
                $qb->orWhere('p.name like :namep')
                    ->setParameter('namep', '%' . $data['name'] . '%');
                $qb->orWhere('v.name like :namev')
                    ->setParameter('namev', '%' . $data['name'] . '%');
                $qb->orWhere('c.name like :namec')
                    ->setParameter('namec', '%' . $data['name'] . '%');
            }

            if ($data['vendors']->count() > 0) {
                $qb->andWhere('p.vendor in (:vendors)')
                    ->setParameter('vendors', $data['vendors']);
            }

            $products = $qb->getQuery()->getResult();
        } else {
            $products = $category->getProducts();
        }
        $requestData = $request->request->all();

        // VerificÄƒ dacÄƒ existÄƒ cheia 'category' Ã®n requestData['form']
        if (isset($requestData['form']) && isset($requestData['form']['category'])) {
            $categoryName = $requestData['form']['category'];
            $category = $categoryRepository->findOneBy(['name' => $categoryName]);
        } else {
            // GestioneazÄƒ cazul Ã®n care cheia 'category' nu existÄƒ
            $category = null;
        }


        return $this->render('category/index.html.twig',
            [
                'form' => $form->createView(),
                'category' => $category,
                'categories' => $categoryRepository->findAll(),
                'vendors' => $vendorRepository->findAll(),
                'products' => $products
            ]);
    }
}
