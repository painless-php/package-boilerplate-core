<?php

namespace PainlessPHP\Package\Boilerplate\Core\Version;

use Bartlett\CompatInfo\Application\Analyser\CompatibilityAnalyser;
use PainlessPHP\Package\Boilerplate\Core\Helpers;
use PainlessPHP\Package\Boilerplate\Core\Exception\VersionAnalysisException;

class VersionAnalysisReport
{
    public function __construct(
        private SemanticVersionNumber $minimumVersion,
        private array $requiredExtensions
    )
    {
    }

    public static function fromCompatinfo(array $data) : static
    {
        $topLevel = CompatibilityAnalyser::class;

        $paths = [
            "$topLevel~versions~php.min",
            "$topLevel~extensions"
        ];

        if(Helpers::nestedArrayKeysExist($data, $paths, '~')) {
            $msg = 'Could not find the expected data from compatinfo report';
            throw new VersionAnalysisException($msg);
        }

        return static::fromArray([
            'minimumVersion' => SemanticVersionNumber::fromString($data[$topLevel]['versions']['php.min']),
            'requiredExtensions' => array_keys($data[$topLevel]['extensions'])
        ]);
    }

    public static function fromCompatinfoFile(string $filepath) : static
    {
        if(! file_exists($filepath)) {
            $msg = "Could not create version report from file: '$filepath' does not exist.";
            throw new VersionAnalysisException($msg);
        }

        $json = json_decode(
            json: file_get_contents($filepath),
            associative: true,
            flags: JSON_THROW_ON_ERROR
        );

        return static::fromCompatinfo($json);
    }

    public static function fromArray(array $data) : static
    {
        return new static(...$data);
    }

    public function getMinimumVersion() : SemanticVersionNumber
    {
        return $this->minimumVersion;
    }

    public function getRequiredExtensions(array $ignoredExtensions = ['core', 'standard']) : array
    {
        return array_values(array_diff($this->requiredExtensions, $ignoredExtensions));
    }
}
