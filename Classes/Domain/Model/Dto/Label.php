<?php

declare(strict_types=1);

namespace StudioMitte\Csv2Xlf\Domain\Model\Dto;

readonly class Label
{
    public function __construct(
        public string $key,
        public string $language,
        public string $source,
        public string $translation = '',
        public string $approved = ''

    ) {}

    public function isDefault(): bool
    {
        return $this->language === 'en';
    }
}
