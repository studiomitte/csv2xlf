<?php

$EM_CONF[$_EXTKEY] = [
    'title' => 'Convert CSV to XLF',
    'description' => 'Provide command to convert given csv to a XLF file',
    'category' => 'misc',
    'constraints' => [
        'depends' => [
            'typo3' => '12.4.0-12.9.99',
        ],
        'conflicts' => [
        ],
    ],
    'autoload' => [
        'psr-4' => [
            'StudioMitte\\Csv2Xlf\\' => 'Classes',
        ],
    ],
    'state' => 'beta',
    'version' => '0.1.0',
];
