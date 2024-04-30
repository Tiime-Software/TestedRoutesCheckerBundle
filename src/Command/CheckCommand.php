<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\RouterInterface;
use Tiime\TestedRoutesCheckerBundle\RouteStorage\RouteStorageInterface;

#[AsCommand(
    name: 'tiime:tested-routes-checker:check',
    description: 'Ensure all routes have been tested during previous PHPUnit run.',
)]
class CheckCommand extends Command
{
    public function __construct(
        private readonly RouterInterface $router,
        private readonly RouteStorageInterface $routeStorage,
        private readonly int $maximumNumberOfNonTestedRoutesToDisplay = 25,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('maximum-routes-to-display', 'm', InputOption::VALUE_REQUIRED, 'Maximum number of non tested routes to display', $this->maximumNumberOfNonTestedRoutesToDisplay)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $testedRoutes = $this->routeStorage->getRoutes();

        $routes = array_keys($this->router->getRouteCollection()->all());
        $nonTestedRoutes = array_diff($routes, $testedRoutes);

        if (0 === $count = \count($nonTestedRoutes)) {
            $io->success('Congrats, all routes have been tested!');

            return Command::SUCCESS;
        }

        $io->writeln('Some routes have not been tested :');
        $io->writeln('');

        /* @phpstan-ignore-next-line */
        $max = (int) $input->getOption('maximum-routes-to-display');

        if ($count < $max) {
            $io->listing($nonTestedRoutes);

            $io->error(sprintf('Found %d non tested route%s!', $count, 1 === $count ? '' : 's'));

            return Command::FAILURE;
        }

        $io->listing(\array_slice($nonTestedRoutes, 0, $max));
        $io->writeln(sprintf('... and %d more', $count - $max));

        $io->error("Found $count non tested routes!");

        return Command::FAILURE;
    }
}
