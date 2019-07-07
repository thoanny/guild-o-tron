<?php

namespace App\Controller;

use App\Entity\Guild;
use App\Entity\GuildDecoration;
use App\Form\GuildDecorationFormType;
use App\Utils\Gw2Api;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

class GuildDecorationController extends AbstractController
{

  private $itemsId = [];
  private $upgradesId = [];

  private function searchUserByAccountName($name, $array) {
     foreach ($array as $key => $val) {
       $val = (array) $val;
         if ($val['name'] === $name) {
             return $key;
         }
     }
     return -1;
  }

  private function getIngredients($item) {

    $api = new Gw2Api();
    $recipe = $api->get('/recipes/search', null, null, ['output' => $item]);

    if($recipe) {
      $id = $recipe[0];

      $recipe = $api->get('/recipes/:id', null, ['id' => $id]);
      if($recipe) {

        foreach($recipe->ingredients as $k => $ingredient) {
          $recipe->ingredients[$k]->recipe = $this->getIngredients($ingredient->item_id);
          if(!isset($this->itemsId[$ingredient->item_id])) {
            $this->itemsId[$ingredient->item_id] = $ingredient->count;
          } else {
            $this->itemsId[$ingredient->item_id] = $this->itemsId[$ingredient->item_id] + $ingredient->count;
          }
        }

        if(isset($recipe->guild_ingredients)) {
          foreach($recipe->guild_ingredients as $k => $ingredient) {
            if(!isset($this->upgradesId[$ingredient->upgrade_id])) {
              $this->upgradesId[$ingredient->upgrade_id] = $ingredient->count;
            } else {
              $this->upgradesId[$ingredient->upgrade_id] = $this->upgradesId[$ingredient->upgrade_id] + $ingredient->count;
            }
          }
        }
      }
    }

    return $recipe;

  }

  private function getRecipe($item) {
    $api = new Gw2Api();
    $recipe = $api->get('/recipes/search', null, null, ['output' => $item]);
    $this->itemsId = [];
    $this->upgradesId = [];

    if($recipe) {
      $id = $recipe[0];

      $recipe = $api->get('/recipes/:id', null, ['id' => $id]);
      if($recipe) {

        foreach($recipe->ingredients as $k => $ingredient) {
          $recipe->ingredients[$k]->recipe = $this->getIngredients($ingredient->item_id);
          if(!isset($this->itemsId[$ingredient->item_id])) {
            $this->itemsId[$ingredient->item_id] = $ingredient->count;
          } else {
            $this->itemsId[$ingredient->item_id] = $this->itemsId[$ingredient->item_id] + $ingredient->count;
          }
        }

        if(isset($recipe->guild_ingredients)) {
          foreach($recipe->guild_ingredients as $k => $ingredient) {
            if(!isset($this->upgradesId[$ingredient->upgrade_id])) {
              $this->upgradesId[$ingredient->upgrade_id] = $ingredient->count;
            } else {
              $this->upgradesId[$ingredient->upgrade_id] = $this->upgradesId[$ingredient->upgrade_id] + $ingredient->count;
            }
          }
        }
      }


      $_items = [];

      if($this->itemsId) {
        $ids = implode(',', array_keys($this->itemsId));
        $items = $api->get('/items', null, null, ['ids' => $ids]);

        if($items) {

          foreach($items as $item) {
            $_items[$item->id] = $item;
          }
        }
      }

      if($_items) {
        $recipe->_items = $_items;
      }

      $_upgrades = [];

      if($this->upgradesId) {
        $ids = implode(',', array_keys($this->upgradesId));
        $upgrades = $api->get('/guild/upgrades', null, null, ['ids' => $ids]);

        if($upgrades) {

          foreach($upgrades as $upgrade) {
            $_upgrades[$upgrade->id] = $upgrade;
          }
        }
      }

      if($_upgrades) {
        $recipe->_upgrades = $_upgrades;
      }

      return (array) $recipe;
    }

    return;

  }

  /**
   * @Route("/guilds/{slug}/decorations", name="guild_decorations")
   */
  public function index(string $slug, Request $request)
  {

    $entityManager = $this->getDoctrine()->getManager();
    $guild = $entityManager->getRepository(Guild::class)->findOneBySlug($slug);
    $decorations = $entityManager->getRepository(GuildDecoration::class)->findByGuild($guild);

    $user = $this->getUser();

    $isMember = false;
    if( $user && $this->searchUserByAccountName( $user->getAccountName(), $guild->getGuildMembers()->getMembers() ) >= 0 ) {
      $isMember = true;
    }

    $form = $this->createForm(GuildDecorationFormType::class, new GuildDecoration());
    $form->handleRequest($request);
    if ($form->isSubmitted() && $form->isValid()) {

      $decoration = $form->getData();

      $recipe = $this->getRecipe($decoration->getDecoration()->getItem());
      if(!$recipe) {
        $this->addFlash('error', 'flash.decoration.nodata');
        return $this->redirectToRoute('guild_decorations', ['slug' => $guild->getSlug()]);
      }

      $decoration->setRecipe($recipe);
      $decoration->setGuild($guild);
      $entityManager->persist($decoration);
      $entityManager->flush();
      $this->addFlash('success', 'flash.decoration.added');
      return $this->redirectToRoute('guild_decorations', ['slug' => $guild->getSlug()]);
    }

    return $this->render('guild/decorations/index.html.twig', [
      'decorations' => $decorations,
      'isMember' => $isMember,
      'guild' => $guild,
      'form' => $form->createView()
    ]);
  }
}
