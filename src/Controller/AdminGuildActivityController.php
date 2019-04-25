<?php

namespace App\Controller;

use App\Entity\GuildActivity;
use App\Form\AdminGuildActivityEditType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminGuildActivityController extends AbstractController
{
    /**
     * @Route("/admin/guilds/activities", name="admin_guilds_activities")
     */
    public function index()
    {
     $repository = $this->getDoctrine()->getRepository(GuildActivity::class);
     $activities = $repository->findAll();

     return $this->render('admin/guild/activity/index.html.twig', [
       'activities' => $activities,
     ]);
    }

    /**
     * @Route("admin/guilds/activities/{id}", name="admin_guilds_activities_edit")
     */
    public function edit(Request $request, $id = null) {

      $entityManager = $this->getDoctrine()->getManager();

      if($id === 'new') {

        $activity = new GuildActivity;
        $form = $this->createForm(AdminGuildActivityEditType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $activity = $form->getData();
          $entityManager->persist($activity);
          $entityManager->flush();

          $this->addFlash('success', 'Activité de guilde ajoutée.');
          return $this->redirectToRoute('admin_guilds_activities');
        }

      } else {

        $repository = $this->getDoctrine()->getRepository(GuildActivity::class);
        $activity = $repository->findOneById($id);
        $savedUid = $activity->getUid();

        $form = $this->createForm(AdminGuildActivityEditType::class, $activity);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
          $activity = $form->getData();
          $activity->setUid($savedUid);
          $entityManager->flush();

          $this->addFlash('success', 'Activité de guilde enregistrée.');
          return $this->redirectToRoute('admin_guilds_activities');
        }

      }

      return $this->render('admin/guild/activity/edit.html.twig', [
        'form' => $form->createView(),
        'id' => $id
      ]);
    }
}
