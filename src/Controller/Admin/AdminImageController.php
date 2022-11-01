<?php

namespace App\Controller\Admin;

use App\Entity\Image;
use App\Handler\Form\ImageHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/image')]
class AdminImageController extends AbstractController
{
    #[Route('/{id}/edit', name: 'app_admin_image_edit', methods: ['POST|GET'])]
    public function edit(
        Request      $request,
        Image        $image,
        ImageHandler $imageHandler
    ): Response
    {
        if ($imageHandler->handle($request, $image)) {
            $this->addFlash('success', 'L\'image a bien été modifiée avec succès');
            return $this->redirectToRoute('tricks.show', [
                'slug' => $image->getPost()->getSlug(),
                'id' => $image->getPost()->getId()]);
        }

        return $this->render('admin/image/edit.html.twig', [
            'image' => $image,
            'form' => $imageHandler->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_image_delete', methods: ['DELETE'])]
    public function delete(
        Request                $request,
        Image                  $image,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $request->request->get('_token'))) {
            $entityManager->remove($image);
            $entityManager->flush();
            $this->addFlash('success', 'L\'image a bien été supprimée avec succès');
        }

        return $this->redirectToRoute('tricks.show', [
            'slug' => $image->getPost()->getSlug(),
            'id' => $image->getPost()->getId()
        ]);
    }
}
