<?php

declare(strict_types=1);

namespace StudioMitte\Csv2Xlf\Tests\Service;

use StudioMitte\Csv2Xlf\Service\ConvertService;
use StudioMitte\Csv2Xlf\Service\CsvReader;
use StudioMitte\Csv2Xlf\Service\XlfFileService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class ConvertServiceTest extends UnitTestCase
{
    public function testConversion(): void
    {
        $exampleFile = __DIR__ . '/Fixtures/example.csv';
        $out = __DIR__ . '/Result/result.xlf';
        $forceRebuild = true;

        $xlfService = new XlfFileService();
        $csvReaderService = new CsvReader();
        $convertService = new ConvertService($xlfService, $csvReaderService);
        $convertService->convert($exampleFile, $out, $forceRebuild);

        self::assertFileEquals(__DIR__ . '/Fixtures/result.xlf', $out, 'Conversion failed for default');
        self::assertFileEquals(__DIR__ . '/Fixtures/de.result.xlf', __DIR__ . '/Result/de.result.xlf', 'Conversion failed for de');
    }
}
