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

class ConvertXlf2CsvCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('in', InputArgument::REQUIRED, 'XLF file to convert');
        $this->addArgument('out', InputArgument::REQUIRED, 'Path to save the CSV file');
        $this->addArgument('languages', InputArgument::REQUIRED, 'Comma separated list of languages');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title($this->getDescription());

        $in = $input->getArgument('in') ?? '';
        $out = $input->getArgument('out') ?? '';
        $languages = GeneralUtility::trimExplode(',', $input->getArgument('languages') ?? '', true);

        if (!str_starts_with('/', $in)) {
            $in = Environment::getProjectPath() . '/' . $in;
        }

        if (!is_file($in)) {
            $io->error(sprintf('File "%s" could not be found', $in));
            return Command::FAILURE;
        }
        if (empty($languages)) {
            $io->error('No languages provided');
            return Command::FAILURE;
        }

        $convertService = GeneralUtility::makeInstance(ConvertService::class);
        $statistic = $convertService->convertXlf2Csv($in, $out, $languages);

        return Command::SUCCESS;
    }
}
