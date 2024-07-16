<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle\EventListener;

use Symfony\Component\HttpKernel\Event\RequestEvent;
use Tiime\TestedRoutesCheckerBundle\RouteStorage\RouteStorageInterface;

final class KernelRequestListener
{
    public function __construct(
        private readonly RouteStorageInterface $routeStorage,
    ) {
    }

    public function __invoke(RequestEvent $event): void
    {
        if ('' === $routeName = $event->getRequest()->attributes->getString('_route')) {
            return;
        }

        if (null === $response = $event->getResponse()) {
            return;
        }

        $this->routeStorage->saveRoute($routeName, $response->getStatusCode());
    }
}
