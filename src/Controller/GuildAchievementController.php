<?php

namespace App\Controller;

use App\Entity\Guild;
use App\Entity\GuildAchievement;
use App\Entity\AchievementGuide;
use App\Utils\Gw2Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class GuildAchievementController extends AbstractController
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

  private function getGuildAchievements() {

    $filesystem = new Filesystem();
    $file = $this->getParameter('kernel.project_dir') . '/var/tmp/guild-achievements.json';

    if($filesystem->exists($file)) {
      $lastEdit = date('d/m/Y H:i:s', filemtime($file));
      return json_decode(file_get_contents($file), 1);
    }

    $api = new Gw2Api();

    /* Exclude dailies */
    $exclude = [
      79, // Succès quotidiens d'Halloween
      88, // Fractales quotidiennes
      97, // Quotidien
      98, // Succès quotidiens d'Hivernel
      142, // Marais de la pierre de sang quotidien
      145, // Baie des braises quotidien
      149, // Confins de Givramer quotidien
      159, // Lac Doric quotidien
      162, // Festival de la Super Adventure quotidien
      163, // Mont Draconis quotidien
      172, // Plage des sirènes quotidienne
      201, // Nouvel an lunaire quotidien
      204, // Îles de Ventesable quotidiennes
      207, // Chasseur de trésor englouti quotidien
      208, // Domaine de Kourna quotidien
      213, // Festival quotidien des Quatre Vents
      217, // Promontoire de Jahai quotidien
      221, // Course de scaraboules quotidienne
      223, // Pics de Chef-Tonnerre quotidiens
      227, // Chute draconique quotidienne
      194, // Domaine d'Istan quotidien
      102, // Succès quotidiens du nouvel an lunaire
    ];

    $data = [];

    $groups = $api->get('/achievements/groups');

    if($groups) {
      $ids = implode(',', $groups);
      $groups = $api->get('/achievements/groups', null, null, ['ids' => $ids]);

      foreach($groups as $group) {
        $data['groups'][$group->order] = $group;

        if($group->categories) {
          $_categories = [];

          foreach($group->categories as $k => $category) {
            if(!in_array($category, $exclude)) {
              $_categories[] = $category;
            }
          }

          if($_categories) {
            $ids = implode(',', $_categories);
            $categories = $api->get('/achievements/categories', null, null, ['ids' => $ids]);

            foreach($categories as $category) {
              $data['categories'][$group->order][$category->order] = $category;
            }

            ksort($data['categories'][$group->order]);
            $data['categories'][$group->order] = array_values($data['categories'][$group->order]);

          }
        }
      }
    }

    ksort($data['categories']);
    $data['categories'] = array_values($data['categories']);

    if($data['categories']) {
      foreach($data['categories'] as $groups) {

        foreach($groups as $category) {

            if($category->achievements) {
              $ids = implode(',', $category->achievements);
              $achievements = $api->get('/achievements', null, null, ['ids' => $ids]);

              foreach($achievements as $a) {
                if(!isset($data['achievements'][$a->id])) {
                  $data['achievements'][$a->id] = $a;

                  if(isset($a->rewards)) {
                    foreach($a->rewards as $reward) {
                      if($reward->type == 'Title' && !isset($data['_titles'][$reward->id])) {
                        $data['_titles'][$reward->id] = $reward->id;
                      }
                    }
                  }

                  if(isset($a->requirement) and isset($a->tiers)) {
                    $last = count($a->tiers);
                    $data['achievements'][$a->id]->requirement = str_replace('  ', " {$a->tiers[$last-1]->count} ", $a->requirement);
                  }

                  if(isset($a->bits)) {
                    foreach($a->bits as $bit) {
                      if($bit->type == 'Item' && !isset($data['_items'][$bit->id])) {
                        $data['_items'][$bit->id] = $bit->id;
                      } else if($bit->type == 'Minipet' && !isset($data['_minis'][$bit->id])) {
                        $data['_minis'][$bit->id] = $bit->id;
                      } else if($bit->type == 'Skin' && !isset($data['_skins'][$bit->id])) {
                        $data['_skins'][$bit->id] = $bit->id;
                      }
                    }
                  }

                }
              }
            }

        }
      }
    }

    if(isset($data['_titles'])) {
      if($titles = $data['_titles']) {
        $ids_chunk = array_chunk($titles, 150);

        foreach($ids_chunk as $idc) {
          $ids = implode(',', $idc);
          $_titles = $api->get('/titles', null, null, ['ids' => $ids]);

          if($_titles) {
            foreach($_titles as $title) {
              $data['titles'][$title->id] = $title;
            }
          }

        }
      }

      ksort($data['titles']);
    }

    if(isset($data['_items'])) {
      if($items = $data['_items']) {
        $ids_chunk = array_chunk($items, 150);

        foreach($ids_chunk as $idc) {
          $ids = implode(',', $idc);
          $_items = $api->get('/items', null, null, ['ids' => $ids]);

          if($_items) {
            foreach($_items as $item) {
              $data['items'][$item->id] = $item;
            }
          }

        }
      }

      ksort($data['items']);
    }

    if(isset($data['_minis'])) {
      if($minis = $data['_minis']) {
        $ids_chunk = array_chunk($minis, 150);

        foreach($ids_chunk as $idc) {
          $ids = implode(',', $idc);
          $_minis = $api->get('/minis', null, null, ['ids' => $ids]);

          if($_minis) {
            foreach($_minis as $mini) {
              $data['minis'][$mini->id] = $mini;
            }
          }

        }
      }

      ksort($data['minis']);
    }

    if(isset($data['_skins'])) {
      if($skins = $data['_skins']) {
        $ids_chunk = array_chunk($skins, 150);

        foreach($ids_chunk as $idc) {
          $ids = implode(',', $idc);
          $_skins = $api->get('/skins', null, null, ['ids' => $ids]);

          if($_skins) {
            foreach($_skins as $skin) {
              $data['skins'][$skin->id] = $skin;
            }
          }

        }
      }
      ksort($data['skins']);
    }

    unset($data['groups']);
    unset($data['_items']);
    unset($data['_minis']);
    unset($data['_skins']);
    unset($data['_titles']);

    $filesystem->dumpFile($file, json_encode($data));

    return $data;
  }


  private function formatMembers($guild) {

    $entityManager = $this->getDoctrine()->getManager();
    $members = $entityManager->getRepository(GuildAchievement::class)->findByGuild($guild);
    $_members = [];

    // dd($members);

    if($members) {
      foreach($members as $member) {
        $_members[$member->getUser()->getAccountName()] = [];

        $now = new \DateTime('now');
        $interval = $member->getUpdatedAt()->diff($now, true);

        $diff = ($interval->days * 24 * 60) + ($interval->h * 60) + $interval->i;

        if($diff > 15) {

          $api = new Gw2Api();
          $achievements = $api->get('/account/achievements', $member->getUser()->getApiKey());

          if($achievements) {
            $member->setData($achievements);
            $entityManager->flush();

            foreach($achievements as $ach) {
              $_members[$member->getUser()->getAccountName()][$ach['id']] = $ach;
            }
          }

        } else {
          if($member->getData()) {
            foreach($member->getData() as $ach) {
              $_members[$member->getUser()->getAccountName()][$ach['id']] = $ach;
            }
          }
        }

      }
    }

    return $_members;
  }

  private function formatGuides($guides) {
    $_guides = [];

    if($guides) {
      foreach($guides as $guide) {
        $_guides[$guide['id']] = $guide['url'];
      }
    }

    return $_guides;
  }

  /**
   * @Route("/guilds/{slug}/achievements", name="guilds_achievements")
   */
  public function index(string $slug, Request $request)
  {

    $entityManager = $this->getDoctrine()->getManager();
    $guild = $entityManager->getRepository(Guild::class)->findOneBySlug($slug);

    $user = $this->getUser();

    $isMember = false;
    if( $user && $this->searchUserByAccountName( $user->getAccountName(), $guild->getGuildMembers()->getMembers() ) >= 0 ) {
      $isMember = true;
    }

    $hasJoined = $entityManager->getRepository(GuildAchievement::class)->findOneBy(['user' => $user, 'guild' => $guild]);

    $achievements = $this->getGuildAchievements();

    $members = $this->formatMembers($guild);
    $guides = $this->formatGuides( $entityManager->getRepository(AchievementGuide::class)->findByLocale( $request->getLocale() ) );

    return $this->render('guild/achievements/index.html.twig', [
      'guild' => $guild,
      'achievements' => $achievements,
      'guides' => $guides,
      'members' => $members,
      'isMember' => $isMember,
      'hasJoined' => $hasJoined,
    ]);
  }




  /**
   * @Route("/guilds/{slug}/achievements/join", name="guilds_achievements_join")
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

    $hasJoined = $entityManager->getRepository(GuildAchievement::class)->findOneBy(['user' => $user, 'guild' => $guild]);
    if($hasJoined) {
      $this->addFlash('danger', 'flash.already.joined');
      return $this->redirectToRoute('guilds_achievements', ['slug' => $slug]);
    }

    $isMember = false;
    if( $user && $this->searchUserByAccountName( $user->getAccountName(), $guild->getGuildMembers()->getMembers() ) >= 0 ) {
      $isMember = true;
    }

    if(!$isMember) {
      $this->addFlash('danger', 'flash.unauthorized');
      return $this->redirectToRoute('guilds_achievements', ['slug' => $slug]);
    }

    $api = new Gw2Api();
    $achievements = $api->get('/account/achievements', $user->getApiKey());

    $guildAchievement = new GuildAchievement();
    $guildAchievement->setUser($user);
    $guildAchievement->setGuild($guild);
    $guildAchievement->setData($achievements);

    $entityManager->persist($guildAchievement);
    $entityManager->flush();

    $this->addFlash('success', 'achievements.join.success');
    return $this->redirectToRoute('guilds_achievements', ['slug' => $slug]);

  }

  /**
   * @Route("/guilds/{slug}/achievements/leave", name="guilds_achievements_leave")
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

    $hasJoined = $entityManager->getRepository(GuildAchievement::class)->findOneBy(['user' => $user, 'guild' => $guild]);
    if(!$hasJoined) {
      $this->addFlash('danger', 'flash.already.leaved');
      return $this->redirectToRoute('guilds_achievements', ['slug' => $slug]);
    }

    $entityManager->remove($hasJoined);
    $entityManager->flush();

    $this->addFlash('success', 'achievements.leave.success');
    return $this->redirectToRoute('guilds_achievements', ['slug' => $slug]);

  }

}
