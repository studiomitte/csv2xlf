<?php

declare(strict_types=1);

namespace StudioMitte\Csv2Xlf\Service;

use League\Csv\Reader;
use StudioMitte\Csv2Xlf\Domain\Model\Dto\Label;

class CsvReader
{
    public function getFromFile(string $filePath): array
    {
        $csvReader = Reader::createFromPath($filePath, 'r');
        $csvReader->setHeaderOffset(0);

        $header = $csvReader->getHeader();
        if ($header[0] !== 'key') {
            throw new \RuntimeException('CSV file has no "key" column on 1st position', 1719919250);
        }
        if ($header[1] !== 'en') {
            throw new \RuntimeException('CSV file has no "en" column on 2nd position', 1719919251);
        }

        $labels = [];
        foreach ($csvReader->getRecords() as $row) {
            $key = $row['key'];
            $default = $row['en'];
            unset($row['key'], $row['en']);

            $labels['en'][$key] = new Label($key, 'en', $default);

            foreach ($row as $language => $translation) {
                if (!$language) {
                    continue;
                }
                $labels[$language][$key] = new Label($key, $language, $default, $translation, 'yes');
            }
        }

        return $labels;
    }
}
