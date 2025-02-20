<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends AbstractController
{
    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/admin/offers", name="admin_offers")
     */
    public function offersAdmin(): Response
    {
        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/offers", name="user_offers")
     */
    public function offersUser(): Response
    {
        return $this->render('default/index.html.twig');
    }
}
