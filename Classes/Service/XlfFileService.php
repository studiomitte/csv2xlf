<?php

declare(strict_types=1);

namespace StudioMitte\Csv2Xlf\Service;

use StudioMitte\Csv2Xlf\Domain\Model\Dto\Label;
use TYPO3\CMS\Core\Localization\Exception\InvalidXmlFileException;

class XlfFileService
{
    /**
     * @return Label[]
     */
    public function getLabels(string $path, string $language): array
    {
        $xmlContent = file_get_contents($path);
        if ($xmlContent === false) {
            throw new InvalidXmlFileException(
                'The path provided does not point to an existing and accessible file.',
                1719382718
            );
        }
        $newLabels = [];
        $rootXmlNode = simplexml_load_string($xmlContent, \SimpleXMLElement::class, LIBXML_NOWARNING);
        foreach ($rootXmlNode->file->body->children() as $translationElement) {
            if ($translationElement->getName() === 'trans-unit' && !isset($translationElement['restype'])) {
                $approved = (string) ($translationElement['approved'] ?? '');
                $parsedData[(string) $translationElement['id']][0] = [
                    'source' => (string) $translationElement->source,
                    'target' => (string) $translationElement->target,
                ];
                $id = (string) $translationElement['id'];
                $newLabels[$id] = new Label(
                    $id,
                    $language,
                    (string) $translationElement->source,
                    (string) $translationElement->target,
                    $approved
                );
            }
        }
        return $newLabels;
    }

    /**
     * @param Label[] $labels
     * @return string|bool
     */
    public function generateLanguageFile(array $labels, string $targetLanguage = '')
    {
        $isATranslation = $targetLanguage !== 'en';
        $domDocument = new \DOMDocument('1.0', 'utf-8');
        $domDocument->formatOutput = true;

        $domFile = $domDocument->createElement('file');
        $domFile->appendChild(new \DOMAttr('source-language', 'en'));
        if ($isATranslation) {
            $domFile->appendChild(new \DOMAttr('target-language', $targetLanguage));
        }
        $domFile->appendChild(new \DOMAttr('datatype', 'plaintext'));
        $domFile->appendChild(new \DOMAttr('original', 'messages'));
        $domFile->appendChild($domDocument->createElement('header'));
        $domBody = $domDocument->createElement('body');
        $domFile->appendChild($domBody);

        $xliff = $domDocument->createElement('xliff');
        $xliff->appendChild(new \DOMAttr('version', '1.2'));
        $xliff->appendChild(new \DOMAttr('xmlns:t3', 'http://typo3.org/schemas/xliff'));
        $xliff->appendChild(new \DOMAttr('xmlns', 'urn:oasis:names:tc:xliff:document:1.2'));
        $xliff->appendChild($domFile);
        $domDocument->appendChild($xliff);

        foreach ($labels as $label) {
            $transUnit = $domDocument->createElement('trans-unit');
            $transUnit->appendChild(new \DOMAttr('id', $label->key));
            $transUnit->appendChild(new \DOMAttr('resname', $label->key));
            if ($isATranslation && $label->approved) {
                $transUnit->appendChild(new \DOMAttr('approved', $label->approved));
            }
            $source = $domDocument->createElement('source');
            $source->appendChild($domDocument->createTextNode($label->source));
            $transUnit->appendChild($source);
            if ($isATranslation) {
                $target = $domDocument->createElement('target');
                $target->appendChild($domDocument->createTextNode($label->translation));
                $transUnit->appendChild($target);
            }

            $domBody->appendChild($transUnit);
        }

        return $domDocument->saveXML();
    }
}
