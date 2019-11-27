<?php

return EzSystems\EzPlatformCodeStyle\PhpCsFixer\EzPlatformInternalConfigFactory::build()->setFinder(
    PhpCsFixer\Finder::create()
        ->in(__DIR__)
        ->exclude([
            'spec',
            'docs',
            'features',
            'vendor',
        ])
        ->files()->name('*.php')
);
