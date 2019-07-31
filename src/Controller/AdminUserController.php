<?php

namespace App\Controller;

use App\Entity\User;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class AdminUserController extends AbstractController
{
    /**
     * @Route("/admin/users", name="admin_users")
     */
    public function index()
    {
      $repository = $this->getDoctrine()->getRepository(User::class);
      $users = $repository->findAll();

      return $this->render('admin/user/index.html.twig', [
        'users' => $users,
      ]);
    }

    /**
     * @Route("/admin/users/delete/{id}", name="admin_users_delete")
     */
    public function delete($id) {
      $entityManager = $this->getDoctrine()->getManager();
      $repository = $this->getDoctrine()->getRepository(User::class);
      $user = $repository->findOneById($id);

      if($user && !in_array('ROLE_ADMIN', $user->getRoles())) {
        $entityManager->remove($user);
        $entityManager->flush();

        $this->addFlash('success', 'Utilisateur supprimé.');
        return $this->redirectToRoute('admin_users');
      }

      $this->addFlash('danger', 'Utilisateur introuvable.');
      return $this->redirectToRoute('admin_users');
    }
}
