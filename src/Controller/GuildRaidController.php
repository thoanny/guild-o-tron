<?php

namespace App\Controller;

use App\Entity\Guild;
use App\Entity\GuildRaid;
use App\Utils\Gw2Api;
use App\Utils\Uid;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class GuildRaidController extends AbstractController
{

  private function searchUserByAccountName($name, $array) {
     foreach ($array as $key => $val) {
       $val = (array) $val;
         if ($val['name'] === $name) {
             return $key;
         }
     }
     return -1;
  }

  private function updateRoster($guild) {
    $entityManager = $this->getDoctrine()->getManager();
    $roster = $entityManager->getRepository(GuildRaid::class)->findByGuild($guild);
    $api = new Gw2Api();

    if($roster) {
      foreach($roster as $r) {
        $now = new \DateTime('now');
        $interval = $r->getUpdatedAt()->diff($now, true);
        $diff = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

        if($diff > 15) {
          $raids = $api->get('/account/raids', $r->getUser()->getApiKey());

          if($raids) {
            $r->setData($raids);
            $entityManager->flush();
          }
        }
      }
    }

    return $roster;
  }

  /**
   * @Route("/guilds/{slug}/raids", name="guilds_raids")
   */
  public function index(string $slug, Request $request): Response
  {
    $entityManager = $this->getDoctrine()->getManager();
    $guild = $entityManager->getRepository(Guild::class)->findOneBySlug($slug);

    $user = $this->getUser();
    if ($guild->getUser() !== $user) {
      $this->addFlash('danger', 'flash.unauthorized');
      return $this->redirectToRoute('guilds_show', ['slug' => $slug]);
    }

    $isMember = false;
    if( $user && $this->searchUserByAccountName( $user->getAccountName(), $guild->getGuildMembers()->getMembers() ) >= 0 ) {
      $isMember = true;
    }

    $hasJoined = $entityManager->getRepository(GuildRaid::class)->findOneBy(['user' => $user, 'guild' => $guild]);
    $roster = $this->updateRoster($guild);

    $api = new Gw2Api();
    $raids = $api->get('/raids');

    if($raids) {
      $ids = implode(',', $raids);
      $raids = $api->get('/raids', null, null, ['ids' => $ids]);
    }

    return $this->render('guild/raids/index.html.twig', [
      'guild' => $guild,
      'isMember' => $isMember,
      'hasJoined' => $hasJoined,
      'raids' => $raids,
      'roster' => $roster
    ]);

  }

  /**
   * @Route("/guilds/{slug}/raids/join", name="guilds_raids_join")
   * @IsGranted("ROLE_USER")
   */
  public function join(string $slug, Request $request): Response
  {
    $entityManager = $this->getDoctrine()->getManager();
    $guild = $entityManager->getRepository(Guild::class)->findOneBySlug($slug);

    $user = $this->getUser();
    if ($guild->getUser() !== $user) {
      $this->addFlash('danger', 'flash.unauthorized');
      return $this->redirectToRoute('guilds_show', ['slug' => $slug]);
    }

    $hasJoined = $entityManager->getRepository(GuildRaid::class)->findOneBy(['user' => $user, 'guild' => $guild]);
    if($hasJoined) {
      $this->addFlash('danger', 'flash.already.joined');
      return $this->redirectToRoute('guilds_raids', ['slug' => $slug]);
    }

    $isMember = false;
    if( $user && $this->searchUserByAccountName( $user->getAccountName(), $guild->getGuildMembers()->getMembers() ) >= 0 ) {
      $isMember = true;
    }

    if(!$isMember) {
      $this->addFlash('danger', 'flash.unauthorized');
      return $this->redirectToRoute('guilds_raids', ['slug' => $slug]);
    }

    $api = new Gw2Api();
    $raids = $api->get('/account/raids', $user->getApiKey());

    $guildRaid = new GuildRaid();
    $guildRaid->setUser($user);
    $guildRaid->setGuild($guild);
    $guildRaid->setData($raids);

    $entityManager->persist($guildRaid);
    $entityManager->flush();

    $this->addFlash('success', 'raids.join.success');
    return $this->redirectToRoute('guilds_raids', ['slug' => $slug]);

  }

  /**
   * @Route("/guilds/{slug}/raids/leave", name="guilds_raids_leave")
   * @IsGranted("ROLE_USER")
   */
  public function leave(string $slug, Request $request): Response
  {
    $entityManager = $this->getDoctrine()->getManager();
    $guild = $entityManager->getRepository(Guild::class)->findOneBySlug($slug);

    $user = $this->getUser();
    if ($guild->getUser() !== $user) {
      $this->addFlash('danger', 'flash.unauthorized');
      return $this->redirectToRoute('guilds_show', ['slug' => $slug]);
    }

    $hasJoined = $entityManager->getRepository(GuildRaid::class)->findOneBy(['user' => $user, 'guild' => $guild]);
    if(!$hasJoined) {
      $this->addFlash('danger', 'flash.already.leaved');
      return $this->redirectToRoute('guilds_raids', ['slug' => $slug]);
    }

    $entityManager->remove($hasJoined);
    $entityManager->flush();

    $this->addFlash('success', 'raids.leave.success');
    return $this->redirectToRoute('guilds_raids', ['slug' => $slug]);

  }
}
