<?php

namespace App\Controller;

use App\Utils\Uid;
use App\Entity\Guild;
use App\Entity\Event;
use App\Entity\EventRegistration;
use App\Entity\EventComment;
use App\Form\GuildEventAddType;
use App\Form\EventCommentType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;

class EventController extends AbstractController
{
    /**
     * @Route("/events", name="events")
     */
    public function index()
    {
        return $this->render('event/index.html.twig', [
            'controller_name' => 'EventController',
        ]);
    }

    private function searchUserByAccountName($name, $array) {
       foreach ($array as $key => $val) {
         $val = (array) $val;
           if ($val['name'] === $name) {
               return $key;
           }
       }
       return -1;
    }

    /**
     * @Route("/guilds/{slug}/events", name="guilds_events")
     */
    function guilds_events(string $slug) {

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

      return $this->render('guild/events/index.html.twig', [
          'guild' => $guild,
          'isMember' => $isMember
      ]);
    }

    /**
     * @Route("/guilds/{slug}/events/add", name="guilds_events_add")
     */
    function guilds_events_add(Request $request, string $slug) {
      $entityManager = $this->getDoctrine()->getManager();
      $guild = $entityManager->getRepository(Guild::class)->findOneBySlug($slug);

      $user = $this->getUser();
      if ($guild->getUser() !== $user) {
        $this->addFlash('danger', 'flash.unauthorized');
        return $this->redirectToRoute('guilds_show', ['slug' => $slug]);
      }

      $event = new Event();
      $event->setStartAt(new \Datetime('now'));

      $form = $this->createForm(GuildEventAddType::class, $event);
      $form->handleRequest($request);
      if ($form->isSubmitted() && $form->isValid()) {
        $uid = new Uid();
        $uid = $uid->generate();

        $event = $form->getData();
        $event->setUser($user);
        $event->setGuild($guild);
        $event->setUid($uid);
        $entityManager->persist($event);
        $entityManager->flush();

        $this->addFlash('success', 'flash.event.added');
        return $this->redirectToRoute('guilds_events', ['slug' => $slug]);
      }

      $isMember = false;
      if( $user && $this->searchUserByAccountName( $user->getAccountName(), $guild->getGuildMembers()->getMembers() ) >= 0 ) {
        $isMember = true;
      }

      return $this->render('guild/events/add.html.twig', [
        'guild' => $guild,
        'isMember' => $isMember,
        'form' => $form->createView()
      ]);
    }

    /**
     * @Route("/events/json", name="events_json")
     */
    function events_json(Request $request)
    {

      $start = $request->query->get('start');
      $end = $request->query->get('end');

      $entityManager = $this->getDoctrine()->getManager();
      $events = $entityManager->getRepository(Event::class)->findBetween($start, $end);

      $data = [];

      if($events) {
        foreach($events as $event) {

          $start = $event['start_at']->format('Y-m-d H:i:s');
          $end = date('Y-m-d H:i:s', strtotime($start . " + {$event['duration']} hours"));

          $data[] = [
            'title' => implode(' ', [ucfirst($event['name']), '['.$event['guildtag'].']', $event['guildname']]),
            'start' => $start,
            'end' => $end,
            'url' => $this->generateUrl(
              'guilds_events_short',
              ['uid' => $event['uid']]
            ),
            // 'url' => $this->generateUrl(
            //   'guilds_events_show',
            //   ['slug' => $slug, 'uid' => $event['uid']]
            // ),
          ];
        }
      }

      $response = new JsonResponse($data);
      return $response;

    }

    /**
     * @Route("/guilds/{slug}/events/json", name="guilds_events_json")
     */
    function guilds_events_json(Request $request, string $slug)
    {

      $start = $request->query->get('start');
      $end = $request->query->get('end');

      $entityManager = $this->getDoctrine()->getManager();
      $guild = $entityManager->getRepository(Guild::class)->findOneBySlug($slug);
      $events = $entityManager->getRepository(Event::class)->findByGuild($guild, $start, $end);

      $data = [];

      if($events) {
        foreach($events as $event) {

          $start = $event['start_at']->format('Y-m-d H:i:s');
          $end = date('Y-m-d H:i:s', strtotime($start . " + {$event['duration']} hours"));


          $data[] = [
            'title' => $event['name'],
            'start' => $start,
            'end' => $end,
            'url' => $this->generateUrl(
              'guilds_events_short',
              ['uid' => $event['uid']]
            ),
            // 'url' => $this->generateUrl(
            //   'guilds_events_show',
            //   ['slug' => $slug, 'uid' => $event['uid']]
            // ),
          ];
        }
      }

      $response = new JsonResponse($data);
      return $response;

    }

    /**
     * @Route("/guilds/{slug}/events/{uid}", name="guilds_events_show")
     */
    function guilds_events_show(string $slug, string $uid, Request $request): Response {

      $entityManager = $this->getDoctrine()->getManager();
      $guild = $entityManager->getRepository(Guild::class)->findOneBySlug($slug);

      $event = $entityManager->getRepository(Event::class)->findOneBy([
        'uid' => $uid,
        'guild' => $guild
      ]);

      if($guild && !$event) {
        $this->addFlash('danger', 'flash.event.notfound');
        return $this->redirectToRoute('guilds_events', ['slug' => $slug]);
      }

      $user = $this->getUser();
      $isMember = false;
      if( $user && $this->searchUserByAccountName( $user->getAccountName(), $guild->getGuildMembers()->getMembers() ) >= 0 ) {
        $isMember = true;
      }

      $isRegistered = null;
      if($user) {
        $isRegistered = $entityManager->getRepository(EventRegistration::class)->findOneBy([
          'event' => $event,
          'user' => $user
        ]);
      }

      /**
       * Comment
       */

       $comment = new EventComment();
       $form = $this->createForm(EventCommentType::class, $comment);
       $form->handleRequest($request);
       if ($form->isSubmitted() && $form->isValid()) {
         $comment = $form->getData();
         $comment->setUser($user);
         $comment->setEvent($event);
         $entityManager->persist($comment);
         $entityManager->flush();

         $this->addFlash('success', 'flash.event.comment.saved');
         return $this->redirectToRoute('guilds_events_show', ['slug' => $slug, 'uid' => $uid]);
       }


      return $this->render('guild/events/show.html.twig', [
        'guild' => $guild,
        'event' => $event,
        'isMember' => $isMember,
        'isRegistered' => $isRegistered,
        'form' => $form->createView()
      ]);
    }

    /**
     * @Route("/e/{uid}", name="guilds_events_short")
     */
    function guilds_events_short(string $uid) {
      // @todo
      $entityManager = $this->getDoctrine()->getManager();
      $event = $entityManager->getRepository(Event::class)->findOneByUid($uid);

      return $this->redirectToRoute('guilds_events_show', ['slug' => $event->getGuild()->getSlug(), 'uid' => $uid]);
    }

    /**
     * @Route("/guilds/{slug}/events/{uid}/registration", name="guilds_events_registration")
     * @IsGranted("ROLE_USER")
     */
    function guilds_events_registration(string $slug, string $uid) {

      $entityManager = $this->getDoctrine()->getManager();
      $guild = $entityManager->getRepository(Guild::class)->findOneBySlug($slug);

      $event = $entityManager->getRepository(Event::class)->findOneBy([
        'uid' => $uid,
        'guild' => $guild
      ]);

      if($guild && !$event) {
        $this->addFlash('danger', 'flash.event.notfound');
        return $this->redirectToRoute('guilds_events', ['slug' => $slug]);
      }

      $user = $this->getUser();
      $isMember = false;
      if( $user && $this->searchUserByAccountName( $user->getAccountName(), $guild->getGuildMembers()->getMembers() ) >= 0 ) {
        $isMember = true;
      }

      $registration = $entityManager->getRepository(EventRegistration::class)->findOneBy([
        'event' => $event,
        'user' => $user
      ]);

      if($registration) {
        $this->addFlash('danger', 'flash.event.registration.already');
        return $this->redirectToRoute('guilds_events_show', ['slug' => $slug, 'uid' => $uid]);
      }

      if($event->getType() == 'intra' && !$isMember) {
        $this->addFlash('danger', 'flash.event.notfound');
        return $this->redirectToRoute('guilds_events', ['slug' => $slug]);
      }

      $registration = new EventRegistration();
      $registration->setUser($user);
      $registration->setEvent($event);
      $entityManager->persist($registration);
      $entityManager->flush();

      $this->addFlash('success', 'flash.event.registration.added');
      return $this->redirectToRoute('guilds_events_show', ['slug' => $slug, 'uid' => $uid]);

    }

}
