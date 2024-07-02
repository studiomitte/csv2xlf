<?php

declare(strict_types=1);

namespace StudioMitte\Csv2Xlf\Tests\Domain\Model\Dto;

use StudioMitte\Csv2Xlf\Domain\Model\Dto\Label;
use TYPO3\TestingFramework\Core\Unit\UnitTestCase;

class LabelTest extends UnitTestCase
{
    public function testForAllProps(): void
    {
        $label = new Label('key', 'de', 'source', 'translation', 'no');
        self::assertSame('key', $label->key);
        self::assertSame('de', $label->language);
        self::assertSame('source', $label->source);
        self::assertSame('translation', $label->translation);
        self::assertSame('no', $label->approved);
        self::assertFalse($label->isDefault());
    }

    public function testIsDefault(): void
    {
        $label = new Label('key', 'en', 'source');
        self::assertTrue($label->isDefault());
    }
}
