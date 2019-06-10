<?php

namespace App\Controller;

use App\Entity\AchievementGuide;
use App\Form\AdminAchievementGuideEditFormType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminAchievementGuideController extends AbstractController
{
  /**
   * @Route("/admin/achievements-guides", name="admin_achievements_guides")
   */
  public function index()
  {

    $repository = $this->getDoctrine()->getRepository(AchievementGuide::class);
    $guides = $repository->findAll();

    return $this->render('admin/achievement-guide/index.html.twig', [
      'guides' => $guides,
    ]);
  }


  /**
   * @Route("admin/guilds/achievements-guides/{id}", name="admin_achievements_guides_edit")
   */
  public function edit(Request $request, $id = null) {

    $entityManager = $this->getDoctrine()->getManager();

    if($id === 'new') {

      $guide = new AchievementGuide;
      $form = $this->createForm(AdminAchievementGuideEditFormType::class, $guide);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        $guide = $form->getData();
        $entityManager->persist($guide);
        $entityManager->flush();

        $this->addFlash('success', 'Guides de succès ajoutés.');
        return $this->redirectToRoute('admin_achievements_guides');
      }

    } else {

      $repository = $this->getDoctrine()->getRepository(AchievementGuide::class);
      $guide = $repository->findOneById($id);
      $savedAchievement = $guide->getAchievement();

      $form = $this->createForm(AdminAchievementGuideEditFormType::class, $guide);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        $guide = $form->getData();
        $guide->setAchievement($savedAchievement);
        $entityManager->flush();

        $this->addFlash('success', 'Guides de succès enregistrés.');
        return $this->redirectToRoute('admin_achievements_guides');
      }

    }

    return $this->render('admin/achievement-guide/edit.html.twig', [
      'form' => $form->createView(),
      'id' => $id
    ]);
  }
}
