<?php

declare(strict_types=1);

use PhpCsFixer\Fixer\Phpdoc\PhpdocNoEmptyReturnFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocTrimFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocSummaryFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocAddMissingParamAnnotationFixer;
use PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer;
use Symplify\EasyCodingStandard\Config\ECSConfig;

return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests',
    ])
    ->withPhpCsFixerSets(
        perCS20: true,
        doctrineAnnotation: true,
        psr12: true,
        phpCsFixer: false // 👈 this set includes docblock fixers
    )
    ->withPreparedSets(
        arrays: true,
        namespaces: true,
        spaces: true,
        docblocks: false, // 👈 disable docblock rules
        comments: true,
    )
    ->withSkip([
        // these rules are likely causing @internal and @coversNothing:
        PhpCsFixer\Fixer\Phpdoc\PhpdocNoEmptyReturnFixer::class,
        PhpCsFixer\Fixer\Phpdoc\PhpdocAddMissingParamAnnotationFixer::class,
        PhpCsFixer\Fixer\Phpdoc\PhpdocSummaryFixer::class,
        PhpCsFixer\Fixer\Phpdoc\PhpdocOrderFixer::class,
    ]);
