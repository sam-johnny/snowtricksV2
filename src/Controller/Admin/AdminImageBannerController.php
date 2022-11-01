<?php

namespace App\Controller\Admin;

use App\Entity\ImageBanner;
use App\Form\ImageBannerType;
use App\Handler\Form\ImageBannerHandler;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/image/banner')]
class AdminImageBannerController extends AbstractController
{
    #[Route('/{id}/edit', name: 'app_image_banner_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request            $request,
        ImageBanner        $imageBanner,
        ImageBannerHandler $imageBannerHandler
    ): Response
    {
        if ($imageBannerHandler->handle($request, $imageBanner)) {
            $this->addFlash('success', 'L\'image a bien été modifiée avec succès');
            return $this->redirectToRoute('tricks.show', [
                'slug' => $imageBanner->getPost()->getSlug(),
                'id' => $imageBanner->getPost()->getId()]);
        }

        return $this->renderForm('admin/image_banner/edit.html.twig', [
            'image_banner' => $imageBanner,
            'form' => $imageBannerHandler->getForm(),
        ]);
    }

    #[Route('/{id}', name: 'app_image_banner_delete', methods: ['POST'])]
    public function delete(
        Request                $request,
        ImageBanner            $imageBanner,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->isCsrfTokenValid('delete' . $imageBanner->getId(), $request->request->get('_token'))) {
            $entityManager->remove($imageBanner);
            $entityManager->flush();
            $this->addFlash('success', 'L\'image a bien été supprimée avec succès');
        }

        return $this->redirectToRoute('tricks.show', [
            'slug' => $imageBanner->getPost()->getSlug(),
            'id' => $imageBanner->getPost()->getId()]);
    }
}
