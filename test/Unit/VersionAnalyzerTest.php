<?php

namespace Test\Unit;

use PainlessPHP\Package\Boilerplate\Core\Version\SemanticVersionPrecision;
use PHPUnit\Framework\TestCase;
use PainlessPHP\Package\Boilerplate\Core\Version\VersionAnalyzer;

class VersionAnalyzerTest extends TestCase
{
    public function testCodeWithEnumRequiresVersion81()
    {
        $analyzer = new VersionAnalyzer(
            ROOT_PATH . '/vendor/bin/phpcompatinfo',
            ROOT_PATH . '/test/Input/Enum'
        );

        $this->assertEquals('8.1', $analyzer->analyze()->getMinimumVersion()->toString(SemanticVersionPrecision::Minor));
    }

    public function testCodeWithEnumRequiresNoExtensions()
    {
        $analyzer = new VersionAnalyzer(
            ROOT_PATH . '/vendor/bin/phpcompatinfo',
            ROOT_PATH . '/test/Input/Enum'
        );

        $this->assertEmpty($analyzer->analyze()->getRequiredExtensions());
    }
}
