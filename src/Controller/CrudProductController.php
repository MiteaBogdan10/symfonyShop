<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\ProductImage;
use App\Form\Product1Type;
use App\Repository\ProductRepository;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;


/**
 * @IsGranted("ROLE_ADMIN")
 * @Route("/admin/crud/product")
 */
class CrudProductController extends AbstractController
{
    /**
     * @Route("/", name="app_crud_product_index", methods={"GET"})
     */
    public function index(ProductRepository $productRepository): Response
    {
        return $this->render('crud_product/index.html.twig', [
            'products' => $productRepository->findAll(),
        ]);
    }

    /**
     * @Route("/remove_image/{id}", name="crud_product_remove_image")
     * @param ProductImage $image
     * @param Request $request
     * @param ProductRepository $productRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function removeImage(ProductImage $image,Request $request, ProductRepository $productRepository, EntityManagerInterface $entityManager): Response
    {
        $entityManager->remove($image);
        $entityManager->flush();

        return $this->redirectToRoute('app_crud_product_index', [], Response::HTTP_SEE_OTHER);
    }

    /**
     * @Route("/new", name="app_crud_product_new", methods={"GET", "POST"})
     * @param Request $request
     * @param ProductRepository $productRepository
     * @param EntityManagerInterface $entityManager
     * @return Response
     */
    public function new(Request $request, ProductRepository $productRepository, EntityManagerInterface $entityManager): Response
    {
        $product = new Product();
        $form = $this->createForm(Product1Type::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $imageFiles = $form->get('images')->getData();
            foreach ($imageFiles as $imageFile) {
                $imageFile->move('/var/www/html/Bogdan/symfonyShop/public/images', $imageFile->getClientOriginalName());
                $productImage = new ProductImage();
                $productImage->setImage($imageFile->getClientOriginalName());
                $productImage->setProduct($product);
                $entityManager->persist($productImage);
            }

            $entityManager->persist($product);
            $entityManager->flush();


            return $this->redirectToRoute('app_crud_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('crud_product/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_crud_product_show", methods={"GET"})
     */
    public function show(Product $product): Response
    {
        return $this->render('crud_product/show.html.twig', [
            'product' => $product,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_crud_product_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(Product1Type::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $imageFiles = $form->get('images')->getData();
            foreach ($imageFiles as $imageFile) {
                $imageFile->move('/var/www/html/Bogdan/symfonyShop/public/images', $imageFile->getClientOriginalName());
                $productImage = new ProductImage();
                $productImage->setImage($imageFile->getClientOriginalName());
                $productImage->setProduct($product);
                $entityManager->persist($productImage);
            }
            $entityManager->persist($product);
            $entityManager->flush();
            return $this->redirectToRoute('app_crud_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('crud_product/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/{id}", name="app_crud_product_delete", methods={"DELETE"})
     */
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            // 1. Șterge imaginile asociate produsului
            foreach ($product->getProductImages() as $image) {
                $entityManager->remove($image);
            }
            $entityManager->flush(); // Asigură-te că imaginile sunt șterse înainte de produs

            // 2. Șterge produsul
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_crud_product_index', [], Response::HTTP_SEE_OTHER);
    }

}
