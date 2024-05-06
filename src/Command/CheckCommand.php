<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tiime\TestedRoutesCheckerBundle\RoutesChecker;
use Tiime\TestedRoutesCheckerBundle\RouteStorage\FileRouteStorage;

#[AsCommand(
    name: 'tiime:tested-routes-checker:check',
    description: 'Ensure all routes have been tested during previous PHPUnit run.',
)]
class CheckCommand extends Command
{
    public function __construct(
        private readonly RoutesChecker $routesChecker,
        private readonly int $maximumNumberOfNonTestedRoutesToDisplay,
        private readonly string $routesToIgnoreFile,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addOption('maximum-routes-to-display', 'm', InputOption::VALUE_REQUIRED, 'Maximum number of non tested routes to display', $this->maximumNumberOfNonTestedRoutesToDisplay)
            ->addOption('routes-to-ignore', 'i', InputOption::VALUE_REQUIRED, 'A file containing routes to ignore', $this->routesToIgnoreFile)
            ->addOption('generate-baseline', 'g', InputOption::VALUE_NONE, 'Generate the file containing the routes to be ignored')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $routesToIgnore = [];
        $fileRouteStorage = new FileRouteStorage($this->routesToIgnoreFile);

        try {
            /** @var string $routesToIgnoreFile */
            $routesToIgnoreFile = $input->getOption('routes-to-ignore');
            $fileRouteStorage->setFile($routesToIgnoreFile);
            $routesToIgnore = $fileRouteStorage->getRoutes();
        } catch (\InvalidArgumentException $e) {
        }

        $untestedRoutes = $this->routesChecker->getUntestedRoutes($routesToIgnore);

        if (0 === $count = \count($untestedRoutes)) {
            $io->success('Congrats, all routes have been tested!');

            return Command::SUCCESS;
        }

        $io->writeln('Some routes have not been tested :');
        $io->writeln('');

        /* @phpstan-ignore-next-line */
        $max = (int) $input->getOption('maximum-routes-to-display');

        if ($count < $max) {
            $io->listing($untestedRoutes);

            $io->error(sprintf('Found %d non tested route%s!', $count, 1 === $count ? '' : 's'));

            return Command::FAILURE;
        }

        $io->listing(\array_slice($untestedRoutes, 0, $max));
        $io->writeln(sprintf('... and %d more', $count - $max));

        $io->error("Found $count non tested routes!");

        if ($input->getOption('generate-baseline')) {
            $fileRouteStorage
                ->saveRoute(
                    implode(\PHP_EOL, $untestedRoutes)
                );
            $io->writeln('Results saved in '.$fileRouteStorage->getFile());
        }

        return Command::FAILURE;
    }
}
