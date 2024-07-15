<?php

declare(strict_types=1);

namespace StudioMitte\Csv2Xlf\Tests\Service;

use StudioMitte\Csv2Xlf\Service\ConvertService;
use StudioMitte\Csv2Xlf\Service\CsvService;
use StudioMitte\Csv2Xlf\Service\XlfFileService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ConvertServiceTest extends UnitTestCase
{
    public function testConversionCsv2Xlf(): void
    {
        $exampleFile = __DIR__ . '/Fixtures/example.csv';
        $out = __DIR__ . '/Result/result.xlf';
        $forceRebuild = true;

        $xlfService = new XlfFileService();
        $csvService = new CsvService();
        $convertService = new ConvertService($xlfService, $csvService);
        $convertService->convertCsv2Xlf($exampleFile, $out, $forceRebuild);

        self::assertFileEquals(__DIR__ . '/Fixtures/result.xlf', $out, 'Conversion failed for default');
        self::assertFileEquals(__DIR__ . '/Fixtures/de.result.xlf', __DIR__ . '/Result/de.result.xlf', 'Conversion failed for de');
    }

    public function testConversionXlf2Csv(): void
    {
        $exampleFile = __DIR__ . '/Fixtures/xlf2csv/in.xlf';
        $out = __DIR__ . '/Result/result.csv';

        $xlfService = new XlfFileService();
        $csvService = new CsvService();
        $convertService = new ConvertService($xlfService, $csvService);
        $convertService->convertXlf2Csv($exampleFile, $out, ['de', 'fr']);

        self::assertFileEquals(__DIR__ . '/Fixtures/xlf2csv/result.csv', $out, 'Conversion failed for default');
    }
}
