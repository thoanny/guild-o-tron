<?php

namespace App\Controller;

use App\Utils\Gw2Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\JsonResponse;

class UserController extends AbstractController
{
    /**
     * @Route("/user/bank/get", name="user_bank_get")
     */
    public function bank_get(Security $security)
    {

      $user = $security->getUser();

      $api = new Gw2Api();
      $bank = $api->get('/account/bank', $user->getApiKey());

      $data = [];

      // dd($bank);
      if($bank) {
        foreach($bank as $b) {
          if($b) {
            if(isset($data[$b->id])) {
              $data[$b->id] = $data[$b->id] + $b->count;
            } else {
              $data[$b->id] = $b->count;
            }
          }

        }
      }
      // dd($data);

      $response = new JsonResponse($data);
      return $response;
    }

    /**
     * @Route("/user/materials/get", name="user_materials_get")
     */
    public function materials_get(Security $security)
    {

      $user = $security->getUser();

      $api = new Gw2Api();
      $materials = $api->get('/account/materials', $user->getApiKey());

      $data = [];

      if($materials) {
        foreach($materials as $m) {
          if($m->count > 0) {
            $data[$m->id] = $m->count;
          }

        }
      }

      $response = new JsonResponse($data);
      return $response;
    }
}
