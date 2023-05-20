<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use PainlessPHP\Package\Boilerplate\Core\Composer\Composer;
use PainlessPHP\Package\Boilerplate\Core\Version\SemanticVersionPrecision;
use PainlessPHP\Package\Boilerplate\Core\Version\VersionAnalyzer;

class ComposerTest extends TestCase
{
    static $localComposerPath = PROJECT_ROOT . '/composer.json';
    static $outputComposerPath = PROJECT_ROOT . '/test/output/composer.json';

    public function tearDown() : void
    {
        parent::tearDown();

        // Clean up created files
        if(is_file(self::$outputComposerPath)) {
            unlink(self::$outputComposerPath);
        }
    }

    public function testLocateJsonCanLocateComposerJsonInGivenDirectory()
    {
        $this->assertEquals(self::$localComposerPath, Composer::locate(PROJECT_ROOT));
    }

    public function testLocateJsonCanLocateComposerJsonInGivenFile()
    {
        $this->assertEquals(self::$localComposerPath, Composer::locate(self::$localComposerPath));
    }

    public function testLocateJsonCanLocateLocalProjectComposerJson()
    {
        $this->assertEquals(self::$localComposerPath, Composer::locate());
    }

    public function testLocalJsonDataCanBeRead()
    {
        $this->assertIsArray(Composer::local()->toArray());
    }

    public function testEditChangesTheCorrectDataAndDoesNotSaveChangesByDefault()
    {
        $originalComposerData = Composer::local()->toArray();
        $originalComposerRequire = $originalComposerData['require'];

        $composer = Composer::local();
        $newData = ['example/example-package' => '^1.0.0'];
        $composer->edit(fn($data) => [...$data, ...$newData], 'require');

        // Assert that local composer data has not changed
        $this->assertEquals($originalComposerData, Composer::local()->toArray());

        // Assert that composer variable require data has changed
        $this->assertEquals([...$originalComposerRequire, ...$newData], $composer->toArray()['require']);

        // Assert that no composer variable data apart from require has changed
        $this->assertEquals($originalComposerData, [...$composer->toArray(), 'require' => $originalComposerData['require']]);
    }

    public function testUpdatePhpRequirementsUpdatesMinimumVersionAndExtensions()
    {
        $analyzer = VersionAnalyzer::local();
        $composer = new Composer(PROJECT_ROOT . '/test/input/project-with-string-functions/composer.json');
        $report = $analyzer->analyze(dirname($composer->getFilepath()));

        // Assert that correct data was reported for project
        $this->assertEquals('7.1', $report->getMinimumVersion()->toString(SemanticVersionPrecision::Minor));
        $this->assertEquals(['mbstring'], $report->getRequiredExtensions());

        // Assert that require for composer.json is  empty initially
        $this->assertEmpty($composer->toArray()['require']);

        $composer->updatePHPRequirements($report);

        // Assert that require key was created for composer.json by update
        $this->assertArrayHasKey('require', $composer->toArray());

        // Assert that php version and extension were added to requirements
        $this->assertEquals([
            'php' => '>=7.1',
            'ext-mbstring' => '*'
        ], $composer->toArray()['require']);
    }

    public function testWriteWithFilepathDoesNotOverrideOriginalComposerJson()
    {
        $composer = new Composer(PROJECT_ROOT . '/test/input/project-with-string-functions/composer.json');
        $originalData = $composer->toArray();
        $composer->edit(fn() => ['foo/bar' => '^1.0.0'], 'require');
        $composer->write(self::$outputComposerPath);

        $this->assertEquals($originalData, $composer->load()->toArray());
    }

    public function testWriteWithFilepathCreatesNewComposerJsonWithCorrectData()
    {
        $originalComposerPath = PROJECT_ROOT . '/test/input/project-with-string-functions/composer.json';
        $composer = new Composer($originalComposerPath);

        $originalData = $composer->toArray();
        $requireData = ['foo/bar' => '^1.0.0'];

        $composer->edit(fn() => $requireData, 'require');
        $composer = $composer->write(self::$outputComposerPath);

        $this->assertEquals([...$originalData, 'require' => $requireData], $composer->toArray());
    }
}
