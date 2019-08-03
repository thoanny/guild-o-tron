<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class LocaleController extends AbstractController
{

  private $default = 'en';
  private $languages = ['en', 'es', 'fr', 'de'];

  /**
   * @Route("/change-lang/{language}", name="setlocale")
   */
  public function index(Request $request, $language = null)
  {

    $user = $this->getUser();

    if($language && in_array($language, $this->languages)) {
      $this->get('session')->set('_locale', $language);

      if($user) {
        $user->setLocale($language);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->flush();
      }
    }

    $url = $request->headers->get('referer');

    if(!$url || empty($url)) {
      return $this->redirectToRoute('homepage');
    }

    return $this->redirect($url);

  }
}
