<?php

namespace App\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ControllerEvent;
use Twig\Environment;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack ;

class HeaderMenuEventSubscriber implements EventSubscriberInterface
{
    private $twig;
    private $request;

    public function __construct(Environment $twig, RequestStack  $requestStack)
    {
        $this->twig = $twig;
        $this->requestStack = $requestStack;
    }
    public function onKernelController(ControllerEvent $event)
    {
        // ...
    }

    public static function getSubscribedEvents()
    {
        return [
            'kernel.controller' => 'onKernelController',
        ];
    }

    protected function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    public function onControllerEvent(ControllerEvent $event)
    {
        // ...
        $request = $this->getRequest();
        $this->twig->addGlobal('menu', $request->getPathInfo());
    }
}
