<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    /**
     * @Route("/cart", name="cart")
     */
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', ['cart' => $cartService->getCart(),]);
    }

    /**
     * @Route("/cart/{product}/delete", name="cart_delete")
     */
    public function delete(Product $product,CartService $cartService): Response
    {
        $cartService->delete($product);
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/empty", name="cart_empty")
     */
    public function empty(CartService $cartService): Response
    {
        $cartService->empty();
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/{product}/update", name="cart_update")
     */
    public function update(Product $product, Request $request, CartService $cartService): Response
    {
        $cartService->update($product, $request->request->get('quantity'));
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/cart/{product}/add", name="cart_add")
     */
    public function add(Product $product, CartService $cartService): Response
    {
        $cartService->add($product);
        return $this->redirectToRoute('cart');
    }

}
