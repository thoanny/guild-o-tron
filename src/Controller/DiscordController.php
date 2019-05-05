<?php

namespace App\Controller;

use App\Entity\Guild;
use App\Entity\Event;
use App\Entity\EventRegistration;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class DiscordController extends AbstractController
{
    /**
     * @Route("/discord", name="discord")
     */
    public function index()
    {
        return $this->render('discord/index.html.twig', [
            'controller_name' => 'DiscordController',
        ]);
    }

    /**
     * @Route("/discord/rsvp/{token}", name="discord_rsvp")
     */
    public function rsvp(Request $request, $token)
    {

      list($uid, $username, $userid, $guild) = explode(';', base64_decode($token));

      if(!$uid || !$guild || !$username || !$userid) {
        return new JsonResponse([
            'error' => 'Bad request'
        ], 400);
      }

      $entityManager = $this->getDoctrine()->getManager();
      $guild = $entityManager->getRepository(Guild::class)->findOneBy( ['discord_guild_id' => $guild] );

      if(!$guild) {
        return new JsonResponse([
            'error' => 'Guild have not sync Discord with Guild-O-Tron'
        ], 404);
      }

      $event = $entityManager->getRepository(Event::class)->findOneBy([
        'uid' => $uid,
        'guild' => $guild
      ]);

      if(!$event) {
        return new JsonResponse([
            'error' => 'Event not found'
        ], 404);
      }

      $isRegistered = $entityManager->getRepository(EventRegistration::class)->findOneBy([
        'event' => $event,
        'discord_user_id' => $userid
      ]);

      if($isRegistered) {
        return new JsonResponse([
            'message' => 'you\'re already registred to this event.'
        ]);
      }

      $registration = new EventRegistration();
      $registration->setEvent($event);
      $registration->setDiscordUserName($username);
      $registration->setDiscordUserId($userid);

      $entityManager->persist($registration);
      $entityManager->flush();

      return new JsonResponse([
          'message' => 'you\'re registred to event "'. $event->getName() .'".'
      ]);

    }

    /**
     * @Route("/discord/sync/{token}/{gid}", name="discord_sync")
     */
    public function sync(Request $request, $token, $gid)
    {

      if(!$token || !$gid) {
        return new JsonResponse([
            'error' => 'Bad request',
            'token' => $token,
            'gid' => $gid,
            'request' => (array) $request
        ], 400);
      }

      $entityManager = $this->getDoctrine()->getManager();
      $guild = $entityManager->getRepository(Guild::class)->findOneByUid( $token );

      if(!$guild) {
        return new JsonResponse([
            'error' => 'Guild not found'
        ], 404);
      }

      $guild->setDiscordGuildId($gid);
      $entityManager->flush();

      $response = new JsonResponse(['message' => 'Discord\'s guild synced with Guild-O-Tron']);
      return $response;
    }
}
