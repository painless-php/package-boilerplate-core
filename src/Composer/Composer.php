<?php

namespace PainlessPHP\Package\Boilerplate\Core\Composer;

use Exception;
use InvalidArgumentException;
use PainlessPHP\Package\Boilerplate\Core\Helpers;
use PainlessPHP\Package\Boilerplate\Core\Version\SemanticVersionPrecision;
use PainlessPHP\Package\Boilerplate\Core\Version\VersionAnalysisReport;

class Composer
{
    const FILENAME = 'composer.json';

    private string $filepath;
    private array|null $data = null;

    public function __construct(string $filepath)
    {
        $this->setFilepath($filepath);
    }

    public function load() : self
    {
        return new self($this->filepath);
    }

    public static function local() : self
    {
        return new self(self::locate());
    }

    public static function locate(string $path = __DIR__) : string
    {
        if(is_dir($path)) {
            $path = "$path/composer.json";
        }

        if(is_file($path) && basename($path) === self::FILENAME) {
            return $path;
        }

        $parentDirPath = dirname($path, 2);

        if($parentDirPath === $path) {
            $msg = 'Could not locate composer.json before reaching system root';
            throw new Exception($msg);
        }

        return self::locate("$parentDirPath/composer.json");
    }

    private function setFilepath(string $filepath)
    {
        if(! is_file($filepath)) {
            $msg = "File '$filepath' does not exist";
            throw new InvalidArgumentException($msg);
        }

        $this->filepath = $filepath;
    }

    private function getData() : array
    {
        if($this->data === null) {
            $this->data = $this->loadData();
        }

        return $this->data;
    }

    private function loadData() : array
    {
        return json_decode(file_get_contents($this->filepath), true, JSON_THROW_ON_ERROR);
    }

    public function toArray() : array
    {
        return $this->getData();
    }

    public function edit(callable $callback, string|null $key = null, string $nestingIndicator = null, int $jsonWriteFlags = 0, bool $autoWrite = false) : self
    {
        $data = Helpers::arrayGetNested($this->getData(), $key, $nestingIndicator);
        $data = $callback($data);
        $this->data = Helpers::arraySetNested($this->getData(), $data, $key, $nestingIndicator);

        if($autoWrite) {
            $this->write($jsonWriteFlags);
        }

        return $this;
    }

    public function write(string $filepath = null, int $jsonWriteFlags = 0) : self
    {
        // Nothing to write since data is not loaded
        if($this->data === null) {
            return $this;
        }

        if($filepath === null) {
            $filepath = $this->filepath;
        }

        $data = $this->getData();

        file_put_contents($filepath, json_encode($data, $jsonWriteFlags));
        $composer = new self($filepath);
        $composer->data = $data;

        return $composer;
    }

    public function getFilepath() : string
    {
        return $this->filepath;
    }

    public function updatePHPRequirements(VersionAnalysisReport $report)
    {
        $minPhpVersion = $report->getMinimumVersion()->toString(SemanticVersionPrecision::Minor);
        $requiredExtensions = [];

        // Map composer requirements for PHP extensions
        foreach($report->getRequiredExtensions() as $requiredExtension) {
            $requiredExtensions["ext-$requiredExtension"] = '*';
        }

        $this->edit(function($require) use($minPhpVersion, $requiredExtensions) {

            $nonExtensionRequirements = array_filter(
                $require,
                fn($requirementName) => ! str_starts_with($requirementName, 'ext-'),
                ARRAY_FILTER_USE_KEY
            );

            return [
                ...$nonExtensionRequirements,
                'php' => '>=' . $minPhpVersion,
                ...$requiredExtensions,
            ];
        }, 'require');
    }
}
