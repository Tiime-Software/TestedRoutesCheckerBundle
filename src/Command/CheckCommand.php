<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Tiime\TestedRoutesCheckerBundle\IgnoredRoutesStorage;
use Tiime\TestedRoutesCheckerBundle\RoutesChecker;

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

        /** @var string $routesToIgnoreFile */
        $routesToIgnoreFile = $input->getOption('routes-to-ignore');
        $ignoredRoutesStorage = new IgnoredRoutesStorage($routesToIgnoreFile);

        try {
            $routesToIgnore = $ignoredRoutesStorage->getRoutes();
        } catch (\InvalidArgumentException $e) {
            $io->warning('Unable to load the given file containing routes to ignore.');
        }

        $untestedRoutes = $this->routesChecker->getUntestedRoutes($routesToIgnore);
        $testedIgnoredRoutes = $this->routesChecker->getTestedIgnoredRoutes($routesToIgnore);

        if (0 === $count = \count($untestedRoutes)) {
            if (0 < \count($testedIgnoredRoutes)) {
                $this->showTestedIgnoredRoutesSection($io, $testedIgnoredRoutes);

                return Command::FAILURE;
            }

            $io->success('Congrats, all routes have been tested!');

            return Command::SUCCESS;
        }

        $io->writeln('Some routes have not been tested :');
        $io->writeln('');

        /* @phpstan-ignore-next-line */
        $max = (int) $input->getOption('maximum-routes-to-display');

        $io->listing(\array_slice($untestedRoutes, 0, $max));

        if ($count > $max) {
            $io->writeln(sprintf('... and %d more', $count - $max));
        }

        $io->error(sprintf('Found %d non tested route%s!', $count, 1 === $count ? '' : 's'));

        $this->showTestedIgnoredRoutesSection($io, $testedIgnoredRoutes);

        if ($input->getOption('generate-baseline')) {
            $ignoredRoutesStorage
                ->saveRoute(
                    implode(\PHP_EOL, $untestedRoutes)
                );
            $io->writeln(sprintf('Results saved in %s', $routesToIgnoreFile));
        }

        return Command::FAILURE;
    }

    /**
     * @param string[] $testedIgnoredRoutes
     */
    private function showTestedIgnoredRoutesSection(SymfonyStyle $io, array $testedIgnoredRoutes): void
    {
        if (0 === \count($testedIgnoredRoutes)) {
            return;
        }

        $io->warning('Some ignored routes looks tested, you should remove them from baseline!');
        $io->listing($testedIgnoredRoutes);
    }
}
