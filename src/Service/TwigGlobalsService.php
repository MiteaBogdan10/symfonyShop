<?php

namespace App\Service;

use App\Repository\CategoryRepository;

class TwigGlobalsService
{
    /** @var CategoryRepository */
    private $categoryRepository;

    /**
     * @param CategoryRepository $categoryRepository
     */
    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function getMenuCategories()
    {
        return $this->categoryRepository->findAll();
    }
}