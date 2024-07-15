<?php

declare(strict_types=1);

namespace StudioMitte\Csv2Xlf\Service;

use League\Csv\Reader;
use League\Csv\Writer;
use StudioMitte\Csv2Xlf\Domain\Model\Dto\Label;

class CsvService
{
    public function getFromFile(string $filePath): array
    {
        $reader = Reader::createFromPath($filePath, 'r');
        $reader->setHeaderOffset(0);

        $header = $reader->getHeader();
        if ($header[0] !== 'key') {
            throw new \RuntimeException('CSV file has no "key" column on 1st position', 1719919250);
        }
        if ($header[1] !== 'en') {
            throw new \RuntimeException('CSV file has no "en" column on 2nd position', 1719919251);
        }

        $labels = [];
        foreach ($reader->getRecords() as $row) {
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

    public function generateCsv(string $path, array $data, array $headers)
    {
        $writer = Writer::createFromPath($path, 'w');
        $writer->insertOne($headers);
        $writer->insertAll($data);
    }
}
