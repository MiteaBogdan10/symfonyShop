<?php

namespace App\Command;

use App\Repository\CartRepository;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class CartCleanupCommand extends Command
{
    protected static $defaultName = 'app:cart-cleanup';
    protected static $defaultDescription = 'Remove carts without products';

    /** @var CartRepository */
    protected $cartRepository;

    /** @var EntityManagerInterface */
    protected $entityManager;


    public function __construct(EntityManagerInterface $entityManager, CartRepository $cartRepository)
    {
        parent::__construct('app:cart-cleanup');
        $this->cartRepository = $cartRepository;
        $this->entityManager = $entityManager;
    }


    protected function configure(): void
    {
        $this
            ->setDescription(self::$defaultDescription)
            ->addArgument('product_limit', InputArgument::OPTIONAL, 'Cart product limit', 0)
            ->addOption('list_id', 'l', InputOption::VALUE_NONE, 'List deleted cart Id')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $productLimit = $input->getArgument('product_limit');

        $carts = $this->cartRepository->findAll();

        foreach ($carts as $cart) {
            if(CartService::countCartProduct($cart) <= $productLimit ) {
                $this->entityManager->remove($cart);
                if ($input->getOption('list_id')) {
                    $io->success('Cart with ID ' . $cart->getId() . ' has been deleted.');
                }
                $this->entityManager->flush();
            }
        }
        return 0;
    }

}
