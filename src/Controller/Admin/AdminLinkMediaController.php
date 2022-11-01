<?php

namespace App\Controller\Admin;

use App\Entity\LinkMedia;
use App\Form\LinkMediaType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/link/media')]
class AdminLinkMediaController extends AbstractController
{
    #[Route('/{id}/edit', name: 'app_link_media_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request                $request,
        LinkMedia              $linkMedia,
        EntityManagerInterface $entityManager
    ): Response
    {
        $form = $this->createForm(LinkMediaType::class, $linkMedia);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Le lien a bien été modifiée avec succès');
            return $this->redirectToRoute('tricks.show', [
                'slug' => $linkMedia->getPost()->getSlug(),
                'id' => $linkMedia->getPost()->getId()]);
        }

        return $this->renderForm('admin/link_media/edit.html.twig', [
            'link_media' => $linkMedia,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_link_media_delete', methods: ['DELETE'])]
    public function delete(
        Request                $request,
        LinkMedia              $linkMedia,
        EntityManagerInterface $entityManager
    ): Response
    {
        if ($this->isCsrfTokenValid('delete' . $linkMedia->getId(), $request->request->get('_token'))) {
            $entityManager->remove($linkMedia);
            $entityManager->flush();
            $this->addFlash('success', 'Le lien a bien été supprimée avec succès');
        }

        return $this->redirectToRoute('tricks.show', [
            'slug' => $linkMedia->getPost()->getSlug(),
            'id' => $linkMedia->getPost()->getId()]);
    }
}
