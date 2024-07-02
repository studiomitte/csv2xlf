<?php

declare(strict_types=1);

namespace StudioMitte\Csv2Xlf\Service;

use StudioMitte\Csv2Xlf\Domain\Model\Dto\Label;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ConvertService
{

    public function __construct(
        protected readonly XlfFileService $xlfFileService,
        protected readonly CsvReader $csvReader
    )
    {

    }

    public function convert(string $csvFilePath, string $out, bool $forceRebuild): array
    {
        $stats = [];
        $data = $this->csvReader->getFromFile($csvFilePath);

        foreach ($data as $language => $labels) {
            if ($language !== 'en') {
                $fileInfo = pathinfo($out);

                $targetFilename = sprintf('%s/%s.%s', $fileInfo['dirname'], $language, $fileInfo['basename']);
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

}
