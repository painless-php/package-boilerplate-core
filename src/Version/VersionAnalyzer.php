<?php

namespace PainlessPHP\Package\Boilerplate\Devkit\Version;

use PainlessPHP\Package\Boilerplate\Devkit\Compatinfo\CompatinfoOutputType;
use PainlessPHP\Package\Boilerplate\Devkit\Compatinfo\RunCompatinfo;
use PainlessPHP\Package\Boilerplate\Devkit\Exception\VersionAnalysisException;

class VersionAnalyzer
{
    public function __construct(
        private string $binaryPath,
        private string $projectRoot
    )
    {
    }

    /**
     * @throws VersionAnalysisException
     *
     */
    public function analyze(string|array $exclude = ['vendor', 'test']) : VersionAnalysisReport
    {
        $output = (new RunCompatinfo)(
            binaryPath: $this->binaryPath,
            directory: $this->projectRoot,
            exclude: $exclude,
            output: CompatinfoOutputType::Json
        );

        $words = preg_split('/\s+/', $output);
        $outputFile = null;

        foreach($words as $word) {
            if(str_starts_with($word, '/tmp/')) {
                $outputFile = $word;
            }
        }

        if(empty($outputFile)) {
            $msg = "Could not find the version analysis report file from the output: ";
            $msg .= PHP_EOL . $output;
            throw new VersionAnalysisException($msg);
        }

        return VersionAnalysisReport::fromCompatinfoFile($outputFile);
    }
}
