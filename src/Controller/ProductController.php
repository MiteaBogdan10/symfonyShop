<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Form\ProductType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/admin/product")
 */
class ProductController extends AbstractController
{
    /**
     * @Route("/create", name="product_form")
     */
    public function create(Request $request): Response
    {
        $product = new Product();

        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);
        if($productForm->isSubmitted() && $productForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $imageFiles = $productForm->get('images')->getData();

            foreach ($imageFiles as $imageFile) {
                $imageFile->move('/var/www/html/Bogdan/symfonyShop/public/images', $imageFile->getClientOriginalName());
                $productImage = new ProductImage();
                $productImage->setImage($imageFile->getClientOriginalName());
                $productImage->setProduct($product);
                $em->persist($productImage);
            }


            $em->persist($product);
            $em->flush();
        }

        return $this->render('product/index.html.twig', [
            'productForm' => $productForm->createView(),
        ]);
    }

    /**
     * @Route("/edit/{product}", name="product_edit")
     */
    public function edit(Product $product,Request $request): Response
    {
        $productForm = $this->createForm(ProductType::class, $product);

        $productForm->handleRequest($request);
        if($productForm->isSubmitted() && $productForm->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $imageFiles = $productForm->get('images')->getData();

            foreach ($imageFiles as $imageFile) {
                $imageFile->move('/var/www/html/Bogdan/symfonyShop/public/images', $imageFile->getClientOriginalName());
                $productImage = new ProductImage();
                $productImage->setImage($imageFile->getClientOriginalName());
                $productImage->setProduct($product);
                $em->persist($productImage);
            }


            $em->persist($product);
            $em->flush();
        }

        return $this->render('product/index.html.twig', [
            'productForm' => $productForm->createView(),
        ]);
    }
}
