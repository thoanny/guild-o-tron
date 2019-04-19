<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriber implements EventSubscriberInterface
{

  private $defaultLocale;

  public function __construct($defaultLocale)
  {
    $this->defaultLocale = $defaultLocale;
  }

  public function onKernelRequest(GetResponseEvent $event)
  {
    $request = $event->getRequest();
    if (!$request->hasPreviousSession()) {
      return;
    }

    $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
  }

  public static function getSubscribedEvents()
  {
    return [
     KernelEvents::REQUEST => [['onKernelRequest', 20]],
    ];
  }
}
