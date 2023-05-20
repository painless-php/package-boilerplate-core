<?php

namespace PainlessPHP\Package\Boilerplate\Core\Version;

use PainlessPHP\Package\Boilerplate\Core\Compatinfo\CompatinfoOutputType;
use PainlessPHP\Package\Boilerplate\Core\Compatinfo\RunCompatinfo;
use PainlessPHP\Package\Boilerplate\Core\Exception\VersionAnalysisException;
use PainlessPHP\Package\Boilerplate\Core\Composer\Composer;

class VersionAnalyzer
{
    public function __construct(
        private string $binaryPath,
    )
    {
    }

    public static function local() : self
    {
        return new self(dirname(Composer::locate()) . '/vendor/bin/phpcompatinfo');
    }

    /**
     * @throws VersionAnalysisException
     *
     */
    public function analyze(string $targetPath, string|array $exclude = ['vendor', 'test']) : VersionAnalysisReport
    {
        $result = (new RunCompatinfo)(
            binaryPath: $this->binaryPath,
            targetPath: $targetPath,
            exclude: $exclude,
            output: CompatinfoOutputType::Json
        );

        $words = preg_split('/\s+/', $result->getOutput());
        $outputFile = null;

        foreach($words as $word) {
            if(str_starts_with($word, '/tmp/')) {
                $outputFile = $word;
            }
        }

        if(empty($outputFile)) {
            $msg = 'Could not find the version analysis report file';
            $msg .= PHP_EOL . "Command: '{$result->getCommand()}'";
            $msg .= PHP_EOL . "Output: '{$result->getOutput()}'";
            throw new VersionAnalysisException($msg);
        }

        return VersionAnalysisReport::fromCompatinfoFile($outputFile);
    }
}
