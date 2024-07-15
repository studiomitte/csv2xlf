<?php

declare(strict_types=1);

namespace StudioMitte\Csv2Xlf\Tests\Service;

use StudioMitte\Csv2Xlf\Service\CsvService;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class CsvServiceTest extends UnitTestCase
{
    protected CsvService $subject;

    public function setUp(): void
    {
        $this->subject = new CsvService();
        parent::setUp();
    }

    public function testForKeyAs1stHeaderField(): void
    {
        $this->expectExceptionCode(1719919250);
        $this->subject->getFromFile(__DIR__ . '/Fixtures/reader/no-header.csv');
    }

    public function testForEnAs2ndHeaderField(): void
    {
        $this->expectExceptionCode(1719919251);
        $this->subject->getFromFile(__DIR__ . '/Fixtures/reader/no-en.csv');
    }

    /**
     * @throws \JsonException
     */
    public function testCsvReading(): void
    {
        $labels = $this->subject->getFromFile(__DIR__ . '/Fixtures/reader/valid.csv');
        $json = json_encode($labels, JSON_THROW_ON_ERROR | JSON_UNESCAPED_UNICODE);

        self::assertJsonStringEqualsJsonFile(__DIR__ . '/Fixtures/reader/valid.json', $json);
    }
}
