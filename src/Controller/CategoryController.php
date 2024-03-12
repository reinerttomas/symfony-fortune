<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/category/{id}', name: 'app_category_show')]
    public function show(int $id, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findWithFortunesJoin($id);

        if ($category === null) {
            throw $this->createNotFoundException();
        }

        return $this->render('category/show.html.twig', [
            'category' => $category,
        ]);
    }
}
