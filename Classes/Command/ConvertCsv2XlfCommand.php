<?php

declare(strict_types=1);

namespace StudioMitte\Csv2Xlf\Command;

use StudioMitte\Csv2Xlf\Service\ConvertService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use TYPO3\CMS\Core\Core\Environment;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConvertCsv2XlfCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('in', InputArgument::REQUIRED, 'CSV file to convert');
        $this->addArgument('out', InputArgument::REQUIRED, 'Path to save the XLF file');
        $this->addArgument('rebuild', InputArgument::OPTIONAL, 'Force clean build of XLF file');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $in = $input->getArgument('in') ?? '';
        $out = $input->getArgument('out') ?? '';

        if (!str_starts_with('/', $out)) {
            $out = Environment::getProjectPath() . '/' . $out;
        }

        $rebuild = (bool) ($input->getArgument('rebuild') ?? false);

        if (!file_exists($in)) {
            $io->error(sprintf('File "%s" does not exist', $in));
            return Command::FAILURE;
        }

        $outDir = dirname($out);
        if (!is_dir($outDir)) {
            $io->error(sprintf('Target directory "%s" does not exist', $outDir));
            return Command::FAILURE;
        }
        if (!is_writable($outDir)) {
            $io->error(sprintf('Target directory "%s" is not writable', $outDir));
            return Command::FAILURE;
        }

        $convertService = GeneralUtility::makeInstance(ConvertService::class);
        $statistic = $convertService->convertCsv2Xlf($in, $out, $rebuild);

        $io->table(['Language', 'Count'], array_map(fn ($k, $v) => [$k, $v], array_keys($statistic), $statistic));

        return Command::SUCCESS;
    }
}
