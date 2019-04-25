<?php

namespace App\Controller;

use App\Entity\GuildTag;

use App\Form\AdminGuildTagEditType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminGuildTagController extends AbstractController
{
    /**
     * @Route("/admin/guilds/tags", name="admin_guilds_tags")
     */
    public function index()
    {
      $repository = $this->getDoctrine()->getRepository(GuildTag::class);
      $tags = $repository->findAll();

      return $this->render('admin/guild/tag/index.html.twig', [
        'tags' => $tags,
      ]);
    }
    /**
     * @Route("admin/guilds/tags/{id}", name="admin_guilds_tags_edit")
     */
    public function edit(Request $request, $id = null) {

      $entityManager = $this->getDoctrine()->getManager();

      if($id === 'new') {

        $tag = new GuildTag;
        $form = $this->createForm(AdminGuildTagEditType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $tag = $form->getData();
          $entityManager->persist($tag);
          $entityManager->flush();

          $this->addFlash('success', 'Mot-clé de guilde ajouté.');
          return $this->redirectToRoute('admin_guilds_tags');
        }

      } else {

        $repository = $this->getDoctrine()->getRepository(GuildTag::class);
        $tag = $repository->findOneById($id);
        $savedUid = $tag->getUid();

        $form = $this->createForm(AdminGuildTagEditType::class, $tag);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $tag = $form->getData();
          $tag->setUid($savedUid);
          $entityManager->flush();

          $this->addFlash('success', 'Mot-clé de guilde enregistré.');
          return $this->redirectToRoute('admin_guilds_tags');
        }

      }

      return $this->render('admin/guild/tag/edit.html.twig', [
        'form' => $form->createView(),
        'id' => $id
      ]);
    }
}
