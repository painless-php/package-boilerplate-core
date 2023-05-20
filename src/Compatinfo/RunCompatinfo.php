<?php

namespace PainlessPHP\Package\Boilerplate\Core\Compatinfo;

class RunCompatinfo
{
    public function __invoke(
        string $binaryPath,
        string $directory,
        string|array $exclude = ['vendor', 'test'],
        CompatinfoOutputType $output = CompatinfoOutputType::Console
    ) : string|null
    {
        $command = "$binaryPath analyser:run $directory --output=$output->value";

        $exludeOptions = match(gettype($exclude)) {
            'string' => "--exclude=$exclude",
            'array' => implode(' ' , array_map(fn($excluded) => "--exclude=$excluded", $exclude))
        };

        return shell_exec("$command $exludeOptions");
    }
}
