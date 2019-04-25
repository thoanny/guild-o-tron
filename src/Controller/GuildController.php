<?php

namespace App\Controller;

use App\Entity\Guild;
use App\Entity\GuildLog;
use App\Entity\GuildStash;
use App\Entity\GuildMember;
use App\Entity\GuildTreasury;
use App\Form\GuildFormType;
use App\Form\GuildSettingsFormType;
use App\Form\GuildEditFormType;
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
    public function index(Security $security)
    {

      $repository = $this->getDoctrine()->getRepository(Guild::class);
      $guilds = $repository->findGuildForDirectory();
      $user = $security->getUser();

      $myGuilds = null;
      if($user) {
        $myGuilds = $repository->findMyGuilds($user->getAccountName());
      }

      return $this->render('guild/index.html.twig', [
        'guilds' => $guilds,
        'my_guilds' => $myGuilds
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

        $checksum = md5( json_encode( $guild ) );

        $emblem = $api->get('/emblem/foregrounds/:id', null, ['id' => $guild->emblem->foreground->id]);
        if($emblem) {
          $emblem = $emblem->layers[0];
        }

        $user = $security->getUser();

        $newGuild = new Guild;
        $newGuild->setUser($user);
        $newGuild->setTag($guild->tag);
        $newGuild->setName($guild->name);
        $newGuild->setGid($guild->id);
        $newGuild->setLevel($guild->level);
        $newGuild->setCapacity($guild->member_capacity);
        $newGuild->setEmblem($emblem);
        $newGuild->setChecksum($checksum);
        $newGuild->setDisplayStash('members');
        $newGuild->setDisplayTreasury('members');
        $newGuild->setDisplayMembers('members');
        $newGuild->setDisplayInDirectory(0);
        $newGuild->setNotary(0);
        $newGuild->setTavern(0);
        $newGuild->setMine(0);
        $newGuild->setWorkshop(0);
        $newGuild->setMarket(0);
        $newGuild->setArena(0);
        $newGuild->setWarRoom(0);
        $newGuild->setToken($token);

        $entityManager->persist($newGuild);
        $entityManager->flush();

        $this->addFlash('success', 'Guild created.');
        return $this->redirectToRoute('guilds_show', ['slug' => $newGuild->getSlug()]);
      } else {
        return $this->render('guild/add.html.twig');
      }
    }

    /**
     * @Route("/guilds/{slug}/edit", name="guilds_edit")
     * @IsGranted("ROLE_USER")
     */
    public function edit(Request $request, Security $security, string $slug): Response
    {
      $entityManager = $this->getDoctrine()->getManager();
      $guild = $entityManager->getRepository(Guild::class)->findOneBySlug($slug);

      $user = $this->getUser();
      if ($guild->getUser() !== $user) {
        $this->addFlash('danger', 'You can\'t access this page.');
        return $this->redirectToRoute('guilds_show', ['slug' => $slug]);
      }

      $form = $this->createForm(GuildEditFormType::class, $guild);
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $guild = $form->getData();
        $entityManager->flush();

        $this->addFlash('success', 'Homepage saved.');
        return $this->redirectToRoute('guilds_show', ['slug' => $slug]);
      }

      $isMember = false;
      if( $user && $this->searchUserByAccountName( $user->getAccountName(), $guild->getGuildMembers()->getMembers() ) >= 0 ) {
        $isMember = true;
      }

      return $this->render('guild/edit.html.twig', [
        'guild' => $guild,
        'isMember' => $isMember,
        'form' => $form->createView()
      ]);

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

    private function getGuildMembersFromAPI($guild) {

      $api = new Gw2Api();
      $entityManager = $this->getDoctrine()->getManager();

      $guildMembers = $entityManager->getRepository(GuildMember::class)->findOneByGuild($guild);

      if($guildMembers && $guildMembers->getUpdatedAt() <= date('Y-m-d H:i:s', strtotime("-15 min"))) {
        return $guildMembers;
      }

      $members = $api->get('/guild/:id/members', $guild->getToken(), ['id' => $guild->getGid()]);
      $checksum = md5(json_encode($members));

      if($guildMembers && $checksum == $guildMembers->getChecksum()) {
        return $guildMembers;
      }

      if($members) {
        if(!$guildMembers) {
          $guildMembers = new GuildMember;
          $guildMembers->setMembers((array) $members);
          $guildMembers->setChecksum($checksum);
          $guildMembers->setGuild($guild);
          $entityManager->persist($guildMembers);
          $entityManager->flush();
        } else {
          $guildMembers->setMembers((array) $members);
          $guildMembers->setChecksum($checksum);
          $entityManager->flush();
        }
      }

      return $guildMembers;

    }

    private function getGuildLogsFromAPI($guild) {
      $api = new Gw2Api();
      $entityManager = $this->getDoctrine()->getManager();

      $lastGuildLogs = $entityManager->getRepository(GuildLog::class)->findOneByGuild($guild, ['lid' => 'DESC']);

      if($lastGuildLogs) {
        $newLogs = $api->get('/guild/:id/log', $guild->getToken(), ['id' => $guild->getGid()], ['since' => $lastGuildLogs->getLid()]);
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

      $guildLogs = $entityManager->getRepository(GuildLog::class)->findByGuild($guild, ['lid' => 'DESC'], 100);

      return $guildLogs;

    }

    private function getguildTreasuryFromAPI($guild) {

      $api = new Gw2Api();
      $entityManager = $this->getDoctrine()->getManager();

      $guildTreasury = $entityManager->getRepository(GuildTreasury::class)->findOneByGuild($guild);

      // dd(date('Y-m-d H:i:s', strtotime("-15 min")));

      if($guildTreasury && $guildTreasury->getUpdatedAt() <= date('Y-m-d H:i:s', strtotime("-15 min"))) {
        return $guildTreasury;
      }

      $treasury = $api->get('/guild/:id/treasury', $guild->getToken(), ['id' => $guild->getGid()]);
      $checksum = md5( json_encode($treasury) );

      if($guildTreasury && $checksum == $guildTreasury->getChecksum()) {
        return $guildTreasury;
      }

      $items_ids = [];
      $upgrades_ids = [];
      $_items = [];
      $_upgrades = [];
      $_items_total = [];

      if($treasury) {
        foreach($treasury as $k => $tr) {
          if(!in_array($tr->item_id, $items_ids)) {
            $items_ids[] = $tr->item_id;
          }

          $treasury[$k]->needed_total = 0;
          $treasury[$k]->total = 0;

          if(isset($tr->needed_by)) {
            $needed_total = 0;

            foreach($tr->needed_by as $need) {
              if(!in_array($need->upgrade_id, $upgrades_ids)) {
                $upgrades_ids[] = $need->upgrade_id;
              }

              if(isset($need->count)) {
                $needed_total = $needed_total + $need->count;
              }
            }
          }

          $treasury[$k]->needed_total = $needed_total;
          $treasury[$k]->total = round($tr->count / $needed_total * 100, 2);
          $_items_total[$tr->item_id] = $tr->count;
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

      $_guild =  $api->get('/guild/:id', $guild->getToken(), ['id' => $guild->getGid()]);

      if($upgrades_ids) {
        $upgrades_chunk = array_chunk($upgrades_ids, 100);

        foreach($upgrades_chunk as $upgrades) {
          $ids = implode(',', $upgrades);
          $upgrades = $api->get('/guild/upgrades', null, null, ['ids' => $ids]);

          if($upgrades) {
            foreach($upgrades as $upgrade) {
              $_upgrades[$upgrade->id] = $upgrade;

              if(isset($upgrade->costs)) {
                foreach($upgrade->costs as $k => $cost) {
                  if($cost->type == 'Item') {
                    $_upgrades[$upgrade->id]->costs[$k]->total = $_items_total[$cost->item_id];
                    $pc = round($_items_total[$cost->item_id] / $cost->count * 100, 2);
                    $_upgrades[$upgrade->id]->costs[$k]->pc = ($pc > 100) ? 100 : $pc;
                  }
                  if($cost->type == 'Currency' && $cost->name == 'Aetherium') {
                    $pc = round($_guild->aetherium / $cost->count * 100, 2);
                    $_upgrades[$upgrade->id]->costs[$k]->pc = ($pc > 100) ? 100 : $pc;
                  }
                  if($cost->type == 'Collectible' && $cost->item_id == '70701') {
                    $pc = round($_guild->favor / $cost->count * 100, 2);
                    $_upgrades[$upgrade->id]->costs[$k]->pc = ($pc > 100) ? 100 : $pc;
                  }
                }
              }
            }
          }

        }
      }

      $data = [
        'treasury' => $treasury,
        'aetherium' => $_guild->aetherium,
        'favor' => $_guild->favor,
        '_items' => $_items,
        '_upgrades' => $_upgrades,
        '_items_total' => $_items_total
      ];

      if(!$guildTreasury) {
        $guildTreasury = new GuildTreasury;
        $guildTreasury->setTreasury($data);
        $guildTreasury->setChecksum($checksum);
        $guildTreasury->setGuild($guild);
        $entityManager->persist($guildTreasury);
        $entityManager->flush();
      } else {
        $guildTreasury->setTreasury($data);
        $guildTreasury->setChecksum($checksum);
        $entityManager->flush();
      }

      return $guildTreasury;

    }

    private function getGuildFromAPI($slug) {
      $api = new Gw2Api();
      $entityManager = $this->getDoctrine()->getManager();
      $guild = $entityManager->getRepository(Guild::class)->findOneBySlug($slug);
      $apiGuild = $api->get('/guild/:id', $guild->getToken(), ['id' => $guild->getGid()]);

      if($apiGuild) {
        $checksum = md5( json_encode($apiGuild) );

        if($checksum !== $guild->getChecksum()) {

          $emblem = $api->get('/emblem/foregrounds/:id', null, ['id' => $apiGuild->emblem->foreground->id]);
          if($emblem) {
            $emblem = $emblem->layers[0];
          }

          $guild->setLevel($apiGuild->level);
          $guild->setCapacity($apiGuild->member_capacity);
          $guild->setEmblem($emblem);
          $guild->setChecksum($checksum);
          $entityManager->flush();

        }

      }

      return $guild;

    }

    function searchUserByAccountName($name, $array) {
       foreach ($array as $key => $val) {
         $val = (array) $val;
           if ($val['name'] === $name) {
               return $key;
           }
       }
       return -1;
    }

   /**
    * @Route("/guilds/{slug}", name="guilds_show")
    */
    public function show(string $slug): Response
    {
      $api = new Gw2Api();
      $entityManager = $this->getDoctrine()->getManager();
      $guild = $this->getGuildFromAPI($slug);

      $user = $this->getUser();

      $members = $this->getGuildMembersFromAPI($guild);
      $logs = [];

      $isMember = false;
      if( $user && $members && $this->searchUserByAccountName( $user->getAccountName(), $members->getMembers() ) >= 0 ) {
        $isMember = true;
        $logs = $this->getGuildLogsFromAPI($guild);
      }

      return $this->render('guild/show.html.twig', [
        'guild' => $guild,
        'members' => $members,
        'logs' => $logs,
        'isMember' => $isMember
      ]);
    }


  /**
   * @Route("/guilds/{slug}/treasury", name="guilds_treasury")
   */
   public function treasury(string $slug): Response
   {
     $api = new Gw2Api();
     $entityManager = $this->getDoctrine()->getManager();
     $guild = $entityManager->getRepository(Guild::class)->findOneBySlug($slug);
     $user = $this->getUser();

     $isMember = false;
     if( $user && $this->searchUserByAccountName( $user->getAccountName(), $guild->getGuildMembers()->getMembers() ) >= 0 ) {
       $isMember = true;
     }

     if(
       ($guild->getDisplayTreasury() == 'hide') ||
       ($guild->getDisplayTreasury() == 'members' && !$isMember) ||
       ($guild->getDisplayTreasury() == 'logged' && !$user)
     ) {
       return $this->redirectToRoute('guilds_show', ['slug' => $slug]);
     }

     // Update/Get Treasury
     $treasury = $this->getGuildTreasuryFromAPI($guild);

     return $this->render('guild/show.html.twig', [
       'guild' => $guild,
       'treasury' => $treasury,
       'isMember' => $isMember,
       'view' => 'treasury'
     ]);
   }

   /**
    * @Route("/guilds/{slug}/stash", name="guilds_stash")
    */
    public function stash(string $slug): Response
    {
      $api = new Gw2Api();
      $entityManager = $this->getDoctrine()->getManager();
      $guild = $entityManager->getRepository(Guild::class)->findOneBySlug($slug);
      $user = $this->getUser();

      $isMember = false;
      if( $user && $this->searchUserByAccountName( $user->getAccountName(), $guild->getGuildMembers()->getMembers() ) >= 0 ) {
        $isMember = true;
      }

      if(
        ($guild->getDisplayStash() == 'hide') ||
        ($guild->getDisplayStash() == 'members' && !$isMember) ||
        ($guild->getDisplayStash() == 'logged' && !$user)
      ) {
        return $this->redirectToRoute('guilds_show', ['slug' => $slug]);
      }

      // Update Stash
      if(!($stash = $guild->getGuildStash())) {
        $stash = $this->getGuildStashFromAPI($guild);
      } else {
        $this->getGuildStashFromAPI($guild);
      }

      return $this->render('guild/show.html.twig', [
        'guild' => $guild,
        'stash' => $stash,
        'isMember' => $isMember,
        'view' => 'stash'
      ]);
    }

  /**
   * @Route("/guilds/{slug}/members", name="guilds_members")
   */
   public function members(string $slug): Response
   {
     $api = new Gw2Api();
     $entityManager = $this->getDoctrine()->getManager();
     $guild = $entityManager->getRepository(Guild::class)->findOneBySlug($slug);
     $user = $this->getUser();

     $isMember = false;
     if( $user && $this->searchUserByAccountName( $user->getAccountName(), $guild->getGuildMembers()->getMembers() ) >= 0 ) {
       $isMember = true;
     }

     if(
       ($guild->getDisplayMembers() == 'hide') ||
       ($guild->getDisplayMembers() == 'members' && !$isMember) ||
       ($guild->getDisplayMembers() == 'logged' && !$user)
     ) {
       return $this->redirectToRoute('guilds_show', ['slug' => $slug]);
     }

     // Updates Members
     $members = $this->getGuildMembersFromAPI($guild);

     return $this->render('guild/show.html.twig', [
       'guild' => $guild,
       'members' => $members,
       'isMember' => $isMember,
       'view' => 'members'
     ]);
   }

  /**
   * @Route("/guilds/{slug}/settings", name="guilds_settings")
   * @IsGranted("ROLE_USER")
   */
  public function settings(string $slug, Request $request): Response
  {
    $entityManager = $this->getDoctrine()->getManager();
    $guild = $entityManager->getRepository(Guild::class)->findOneBySlug($slug);

    $user = $this->getUser();
    if ($guild->getUser() !== $user) {
      $this->addFlash('danger', 'You can\'t access this page.');
      return $this->redirectToRoute('guilds_show', ['slug' => $slug]);
    }

    $form = $this->createForm(GuildSettingsFormType::class, $guild);
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {
      $guild = $form->getData();
      $entityManager->flush();

      $this->addFlash('success', 'Settings saved.');
      return $this->redirectToRoute('guilds_settings', ['slug' => $slug]);
    }

    $isMember = false;
    if( $user && $this->searchUserByAccountName( $user->getAccountName(), $guild->getGuildMembers()->getMembers() ) >= 0 ) {
      $isMember = true;
    }

    return $this->render('guild/show.html.twig', [
      'view' => 'settings',
      'guild' => $guild,
      'isMember' => $isMember,
      'settings' => $form->createView()
    ]);
  }

}
