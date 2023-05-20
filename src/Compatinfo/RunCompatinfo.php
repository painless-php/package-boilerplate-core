<?php

namespace PainlessPHP\Package\Boilerplate\Core\Compatinfo;

use InvalidArgumentException;
use PainlessPHP\Package\Boilerplate\Core\Console\ConsoleCommandResult;
use PainlessPHP\Package\Boilerplate\Core\Console\RunConsoleCommand;

class RunCompatinfo
{
    public function __invoke(
        string $binaryPath,
        string $targetPath,
        string|array $exclude = ['vendor', 'test'],
        CompatinfoOutputType $output = CompatinfoOutputType::Console
    ) : ConsoleCommandResult
    {

        if(! is_file($binaryPath)) {
            $msg = "Compatinfo binary '$binaryPath' does not exist";
            throw new InvalidArgumentException($msg);
        }

        $resolvedTargetPath = realpath($targetPath);

        if(! file_exists($resolvedTargetPath)) {
            $msg = "Target path '$targetPath' does not exist (resolved as '$resolvedTargetPath')";
            throw new InvalidArgumentException($msg);
        }

        $command = "$binaryPath analyser:run $targetPath --output=$output->value";

        $excludeOptions = match(gettype($exclude)) {
            'string' => "--exclude=$exclude",
            'array' => implode(' ' , array_map(fn($excluded) => "--exclude=$excluded", $exclude))
        };

        return (new RunConsoleCommand)("$command $excludeOptions");
    }
}
