<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryParameter;
use Symfony\Component\Routing\Attribute\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_homepage')]
    public function homepage(
        CategoryRepository $categoryRepository,
        #[MapQueryParameter] ?string $q = null,
    ): Response {
        if ($q !== null) {
            $categories = $categoryRepository->search($q);
        } else {
            $categories = $categoryRepository->findAllOrdered();
        }

        return $this->render('home/index.html.twig', [
            'categories' => $categories,
        ]);
    }
}
