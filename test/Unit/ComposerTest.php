<?php

namespace Test\Unit;

use PHPUnit\Framework\TestCase;
use PainlessPHP\Package\Boilerplate\Devkit\Composer\Composer;

class ComposerTest extends TestCase
{
    static $composerPath = ROOT_PATH . '/composer.json';

    public function testLocateJsonCanLocateComposerJsonInGivenDirectory()
    {
        $this->assertEquals(self::$composerPath, Composer::locateJson(ROOT_PATH));
    }

    public function testLocateJsonCanLocateComposerJsonInGivenFile()
    {
        $this->assertEquals(self::$composerPath, Composer::locateJson(self::$composerPath));
    }

    public function testLocateJsonCanLocateLocalProjectComposerJson()
    {
        $this->assertEquals(self::$composerPath, Composer::locateJson());
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
}
