<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Handler\Form\CategoryHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminCategoryController extends AbstractController
{

    #[Route('/admin/category/new', name: 'category_new', methods: ['GET', 'POST'])]
    public function new(Request $request, CategoryHandler $categoryHandler): Response
    {
       $category = new Category();

        if ($categoryHandler->handle($request, $category)) {
            $this->addFlash('success', 'Bien créé avec succès');
            return $this->redirectToRoute('app.home');
        }

        return $this->renderForm('admin/category/new.html.twig', [
            'category' => $category,
            'form' => $categoryHandler->getForm(),
        ]);
    }

}
