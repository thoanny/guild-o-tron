<?php

namespace App\Controller;

use App\Entity\Decoration;
use App\Form\AdminDecorationEditFormType;
use App\Utils\Gw2Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class AdminDecorationController extends AbstractController
{
  /**
   * @Route("/admin/decorations", name="admin_decorations")
   */
  public function index()
  {

    $repository = $this->getDoctrine()->getRepository(Decoration::class);
    $decorations = $repository->findAll();

    return $this->render('admin/decoration/index.html.twig', [
      'controller_name' => 'AdminDecorationController',
      'decorations' => $decorations
    ]);
  }


  /**
   * @Route("admin/decorations/{id}", name="admin_decorations_edit")
   */
  public function edit(Request $request, $id = null) {

    $entityManager = $this->getDoctrine()->getManager();

    $api = new Gw2Api();

    if($id === 'new') {

      $decoration = new Decoration;
      $form = $this->createForm(AdminDecorationEditFormType::class, $decoration);
      $form->handleRequest($request);

      if ($form->isSubmitted() && $form->isValid()) {
        $decoration = $form->getData();

        $id = $decoration->getItem();

        $langs = ['fr', 'en', 'de', 'es'];
        foreach($langs as $lang) {
          $recipe = $api->get('/items/:id', null, ['id' => $id], null, $lang);

          if($recipe) {
            $_set_lang = 'set'.ucfirst($lang);
            $decoration->$_set_lang($recipe->name);
          }
        }

        $entityManager->persist($decoration);
        $entityManager->flush();

        $this->addFlash('success', 'Décoration ajoutée.');
        return $this->redirectToRoute('admin_decorations');
      }

    }

    return $this->render('admin/decoration/edit.html.twig', [
      'form' => $form->createView(),
      'id' => $id
    ]);
  }

  /**
   * @Route("admin/decorations/{id}/delete", name="admin_decorations_delete")
   */
  public function delete(Request $request, $id = null) {

    $entityManager = $this->getDoctrine()->getManager();

    if($id) {
      $repository = $this->getDoctrine()->getRepository(Decoration::class);
      $decoration = $repository->findOneById($id);

      if($decoration) {
        $entityManager->remove($decoration);
        $entityManager->flush();
      }

      return $this->redirectToRoute('admin_decorations');
    }
  }
}
