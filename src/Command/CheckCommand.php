<?php

declare(strict_types=1);

namespace Tiime\TestedRoutesCheckerBundle\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Routing\RouterInterface;

#[AsCommand(
    name: 'tiime:tested-routes-checker:check',
    description: 'Ensure all routes have been tested during previous PHPUnit run.',
)]
class CheckCommand extends Command
{
    public function __construct(private readonly RouterInterface $router)
    {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->addArgument('file', InputArgument::OPTIONAL, 'The file containing all routes which have been tested', __DIR__.'/../../var/cache/test/tiime_tested_routes')
            ->addOption('maximum-routes-to-display', 'm', InputOption::VALUE_REQUIRED, 'Maximum number of non tested routes to display', 25)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /** @var string $file */
        $file = $input->getArgument('file');

        if (!file_exists($file)) {
            throw new InvalidArgumentException('Given file does not exists!');
        }

        if (false === $testedRoutes = @file($file, \FILE_IGNORE_NEW_LINES)) {
            throw new InvalidArgumentException('Unable to load routes from given file.');
        }

        $routes = array_keys($this->router->getRouteCollection()->all());
        $nonTestedRoutes = array_diff($routes, $testedRoutes);

        $io = new SymfonyStyle($input, $output);

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
