<?php

declare(strict_types=1);

namespace StudioMitte\Csv2Xlf\Service;

use StudioMitte\Csv2Xlf\Domain\Model\Dto\Label;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConvertService
{
    public function __construct(
        protected readonly XlfFileService $xlfFileService,
        protected readonly CsvService $csvService
    ) {}

    public function convertXlf2Csv(string $xlfFilePath, string $out, array $languages): array
    {
        $headers = ['key', 'en'];
        $defaultLabels = $this->xlfFileService->getLabels($xlfFilePath, 'en');
        $translations = [];
        foreach ($languages as $language) {
            if ($language === 'en') {
                throw new \UnexpectedValueException('Language "en" is not allowed', 1721035547);
            }
            $headers[] = $language;
            $path = $this->getXlfFileNameForLanguage($xlfFilePath, $language);
            if (!is_file($path)) {
                continue;
            }
            $translations[$language] = $this->xlfFileService->getLabels($path, $language);
        }

        $flatList = [];

        foreach ($defaultLabels as $label) {
            $flatList[$label->key]['key'] = $label->key;
            $flatList[$label->key]['en'] = $label->source;

            foreach ($languages as $language) {
                if (isset($translations[$language][$label->key])) {
                    $flatList[$label->key][$language] = $translations[$language][$label->key]->translation;
                } else {
                    $flatList[$label->key][$language] = '';
                }
            }
        }

        $this->csvService->generateCsv($out, $flatList, $headers);
        return $flatList;
    }

    public function convertCsv2Xlf(string $csvFilePath, string $out, bool $forceRebuild): array
    {
        $stats = [];
        $data = $this->csvService->getFromFile($csvFilePath);

        foreach ($data as $language => $labels) {
            if ($language !== 'en') {
                $targetFilename = $this->getXlfFileNameForLanguage($out, $language);
            } else {
                $targetFilename = $out;
            }

            if (!$forceRebuild) {
                $labels = $this->addExistingLabels($labels, $language, $targetFilename);
            }
            $stats[$language] = count($labels);

            $xml = $this->xlfFileService->generateLanguageFile($labels, $language);
            GeneralUtility::writeFile($targetFilename, $xml);
        }

        return $stats;
    }

    /**
     * @param Label[] $labels
     * @return Label[]
     */
    protected function addExistingLabels(array $labels, string $language, string $path): array
    {
        if (!file_exists($path)) {
            return $labels;
        }
        $existingLabels = $this->xlfFileService->getLabels($path, $language);

        return array_merge($existingLabels, $labels);
    }

    protected function getXlfFileNameForLanguage(string $out, string $language): string
    {
        $fileInfo = pathinfo($out);

        return sprintf('%s/%s.%s', $fileInfo['dirname'], $language, $fileInfo['basename']);
    }
}
