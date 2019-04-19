<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\SecurityEvents;

class UserLocaleSubscriber implements EventSubscriberInterface
{

  private $session;

  public function __construct(SessionInterface $session)
  {
    $this->session = $session;
  }

  public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
  {
    $user = $event->getAuthenticationToken()->getUser();

    if(null !== $user->getLocale()) {
      $this->session->set('_locale', $user->getLocale());
    }
  }

  public static function getSubscribedEvents()
  {
    return [
      'security.interactive_login' => 'onSecurityInteractiveLogin',
    ];
  }
}
