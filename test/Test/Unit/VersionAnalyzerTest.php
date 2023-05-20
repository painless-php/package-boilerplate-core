<?php

namespace Test\Unit;

use PainlessPHP\Package\Boilerplate\Core\Version\SemanticVersionPrecision;
use PHPUnit\Framework\TestCase;
use PainlessPHP\Package\Boilerplate\Core\Version\VersionAnalyzer;

class VersionAnalyzerTest extends TestCase
{
    public function testCodeWithEnumRequiresVersion81()
    {
        $analyzer = VersionAnalyzer::local();
        $this->assertEquals(
            '8.1',
            $analyzer->analyze(PROJECT_ROOT . '/test/input/project-with-enum')
            ->getMinimumVersion()
            ->toString(SemanticVersionPrecision::Minor)
        );
    }

    public function testCodeWithEnumRequiresNoExtensions()
    {
        $analyzer = VersionAnalyzer::local();
        $this->assertEmpty($analyzer->analyze(PROJECT_ROOT . '/test/input/project-with-enum')->getRequiredExtensions());
    }

    public function testCodeWithStringFunctionsRequiresVersion71()
    {
        $analyzer = VersionAnalyzer::local();
        $this->assertEquals(
            '7.1',
            $analyzer->analyze(PROJECT_ROOT . '/test/input/project-with-string-functions')
            ->getMinimumVersion()
            ->toString(SemanticVersionPrecision::Minor)
        );
    }

    public function testCodeWithStringFunctionsRequiresMbstringExtension()
    {
        $analyzer = VersionAnalyzer::local();
        $this->assertEquals(['mbstring'], $analyzer->analyze(PROJECT_ROOT . '/test/input/project-with-string-functions')->getRequiredExtensions());
    }
}
