<?php

require_once __DIR__ . '/bootstrap.php';

use PainlessPHP\Package\Boilerplate\Core\Version\SemanticVersionPrecision;
use PainlessPHP\Package\Boilerplate\Core\Version\VersionAnalyzer;

$analyzer = new VersionAnalyzer(
    ROOT_PATH . '/vendor/bin/phpcompatinfo',
    ROOT_PATH . '/test/Input/Enum'
);

echo $analyzer->analyze()->getMinimumVersion()->toString(SemanticVersionPrecision::Minor);
