<?php

namespace App\Controller;

use App\Entity\Guild;
use App\Entity\GuildLog;
use App\Entity\GuildStash;
use App\Entity\GuildMember;
use App\Form\GuildFormType;
use App\Utils\Gw2Api;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class GuildController extends AbstractController
{
    /**
     * @Route("/guilds", name="guilds")
     */
    public function index()
    {

      $repository = $this->getDoctrine()->getRepository(Guild::class);
      $guilds = $repository->findAll();

      return $this->render('guild/index.html.twig', [
          'guilds' => $guilds,
      ]);
    }

    /**
     * @Route("/guilds/add", name="guilds_add")
     * @IsGranted("ROLE_USER")
     */
    public function add(Request $request, Security $security): Response
    {

      if ($request->isMethod('POST')) {

        $token = $request->request->get('token');
        $gid = $request->request->get('gid');

        if(!$token || !$gid) {
          $this->addFlash('error', 'GW2 Api key and guild are both required!');
          return $this->redirectToRoute('guilds_add');
        }

        $entityManager = $this->getDoctrine()->getManager();

        $guild = $entityManager->getRepository(Guild::class)->findOneByGid($gid);

        if ($guild !== null) {
          $this->addFlash('danger', 'Guild already exists.');
          return $this->redirectToRoute('guilds');
        }

        $api = new Gw2Api();

        $guilds = [];
        $account = $api->get('/account', $token);

        if($account) {

          if(!isset($account->guild_leader)) {
            $this->addFlash('danger', 'Your are not a leader...');
            return $this->redirectToRoute('guilds');
          }

          if(!in_array($gid, $account->guild_leader)) {
            $this->addFlash('danger', 'Your are not the leader of that guild.');
            return $this->redirectToRoute('guilds');
          }

        }

        $guild = $api->get('/guild/:id', $token, ['id' => $gid]);

        if(!$guild) {
          $this->addFlash('danger', 'We can\'t access to the guild informations.');
          return $this->redirectToRoute('guilds');
        }

        $user = $security->getUser();

        $newGuild = new Guild;
        $newGuild->setUser($user);
        $newGuild->setTag($guild->tag);
        $newGuild->setName($guild->name);
        $newGuild->setGid($guild->id);
        $newGuild->setToken($token);

        $entityManager->persist($newGuild);
        $entityManager->flush();

        $this->addFlash('notice', 'Guild created.');
        return $this->redirectToRoute('guilds');
      } else {
        return $this->render('guild/add.html.twig');
      }
    }

    /**
     * @Route("/guilds/ajax/get", name="guilds_ajax_get")
     * @IsGranted("ROLE_USER")
     */

     public function ajax_get(Request $request): Response
     {

       if($request->isXmlHttpRequest()) {

         $api = new Gw2Api();

         $token = $request->query->get('token');

         $guilds = [];
         $account = $api->get('/account', $token);

         if($account && isset($account->guild_leader)) {
           foreach ($account->guild_leader as $gid) {
             $guild = $api->get('/guild/:gid', null, ['gid' => $gid]);
             if($guild) {
               $guilds[$gid] = "[{$guild->tag}] {$guild->name}";
             }
           }
         }

         if(!$guilds) {
           return new JsonResponse(
             [
                 'status' => 'error',
                 'error' => 'No guild found.',
             ],
             JsonResponse::HTTP_BAD_REQUEST
           );
         }

         $response = new JsonResponse(['guilds' => $guilds]);
         return $response;

       }

       return $this->redirectToRoute('guilds');

     }


    private function getGuildStashFromAPI($guild) {

      $api = new Gw2Api();
      $entityManager = $this->getDoctrine()->getManager();

      $stash = $api->get('/guild/:id/stash', $guild->getToken(), ['id' => $guild->getGid()]);

      if(!$stash) {
        return;
      }

      $checksum = md5(json_encode($stash));
      $guildStash = $entityManager->getRepository(GuildStash::class)->findOneByGuild( $guild );

      if($guildStash && $guildStash->getChecksum() == $checksum) {
        return;
      }

      $items_ids = [];
      $_items = [];
      $upgrades_ids = [];
      $_upgrades = [];

      foreach($stash as $st) {
        $upgrades_ids[] = $st->upgrade_id;

        foreach($st->inventory as $inv) {
          if($inv && !in_array($inv->id, $items_ids)) {
            $items_ids[] = $inv->id;
          }
        }
      }

      if($upgrades_ids) {
        $ids = implode(',', $upgrades_ids);
        $upgrades = $api->get('/guild/upgrades', null, null, ['ids' => $ids]);

        if($upgrades) {
          foreach($upgrades as $upgrade) {
            $_upgrades[$upgrade->id] = $upgrade;
          }
        }
      }

      if($items_ids) {
        $items_chunk = array_chunk($items_ids, 100);

        foreach($items_chunk as $items) {
          $ids = implode(',', $items);
          $items = $api->get('/items', null, null, ['ids' => $ids]);

          if($items) {
            foreach($items as $item) {
              $_items[$item->id] = $item;
            }
          }

        }
      }

      $data = [
        'stash' =>      (array) $stash,
        '_items' =>     (array) $_items,
        '_upgrades' =>  (array) $_upgrades,
      ];

      if(!$guildStash) {
        $guildStash = new GuildStash;
        $guildStash->setStash($data);
        $guildStash->setChecksum($checksum);
        $guildStash->setGuild($guild);
        $entityManager->persist($guildStash);
        $entityManager->flush();
      } else {
        $guildStash->setStash($data);
        $guildStash->setChecksum($checksum);
        $entityManager->flush();
      }

      return $guildStash;
    }

     /**
      * @Route("/guilds/{slug}", name="guilds_show")
      */
    public function show(string $slug): Response
    {
      $api = new Gw2Api();
      $entityManager = $this->getDoctrine()->getManager();
      $guild = $entityManager->getRepository(Guild::class)->findOneBySlug($slug);

      if(!($stash = $guild->getGuildStash())) {
        $stash = $this->getGuildStashFromAPI($guild);
      } else {
        $this->getGuildStashFromAPI($guild);
      }

      // Update guild logs
      $latestLogs = $entityManager->getRepository(GuildLog::class)->findOneBy( [],
        [ 'lid' => 'DESC' ]
      );

      $newLogs = [];
      if($latestLogs) {
        $newLogs = $api->get('/guild/:id/log', $guild->getToken(), ['id' => $guild->getGid()], ['since' => $latestLogs->getLid()]);
      } else {
        $newLogs = $api->get('/guild/:id/log', $guild->getToken(), ['id' => $guild->getGid()]);
      }

      if($newLogs) {
        foreach($newLogs as $log) {

          if(isset($log->user) && isset($log->type)) {
            $newLog = new GuildLog;
            $newLog->setCreatedAt(new \DateTime($log->time));
            $newLog->setUserName($log->user);
            $newLog->setType($log->type);
            $newLog->setData((array) $log);
            $newLog->setGuild($guild);
            $newLog->setLid($log->id);
            $entityManager->persist($newLog);
          }

        }

        $entityManager->flush();
      }

      // Update guild members
      $members = $api->get('/guild/:id/members', $guild->getToken(), ['id' => $guild->getGid()]);
      if($members) {

        $checksum = md5(json_encode($members));
        $guildMembers = $entityManager->getRepository(GuildMember::class)->findOneByGuild( $guild );

        if(!$guildMembers) {
          $newMembers = new GuildMember;
          $newMembers->setMembers((array) $members);
          $newMembers->setChecksum($checksum);
          $newMembers->setGuild($guild);
          $entityManager->persist($newMembers);
          $entityManager->flush();
        } elseif($guildMembers->getChecksum() !== $checksum) {
          $guildMembers->setMembers((array) $members);
          $guildMembers->setChecksum($checksum);
          $entityManager->flush();
        }

      }

      return $this->render('guild/show.html.twig', [
        'guild' => $guild,
        'logs' => $guild->getGuildLogs(),
        'members' => $guild->getGuildMembers(),
        'stash' => $stash
      ]);
    }

}
