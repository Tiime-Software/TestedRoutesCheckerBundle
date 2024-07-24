<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle\EventListener;

use Symfony\Component\HttpKernel\Event\ResponseEvent;
use Tiime\TestedRoutesCheckerBundle\RouteStorage\RouteStorageInterface;

final class KernelRequestListener
{
    public function __construct(
        private readonly RouteStorageInterface $routeStorage,
    ) {
    }

    public function __invoke(ResponseEvent $event): void
    {
        if ('' === $routeName = $event->getRequest()->attributes->getString('_route')) {
            return;
        }

        $this->routeStorage->saveRoute($routeName, $event->getResponse()->getStatusCode());
    }
}
