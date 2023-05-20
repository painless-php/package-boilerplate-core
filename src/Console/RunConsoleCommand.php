<?php

namespace PainlessPHP\Package\Boilerplate\Core\Console;

class RunConsoleCommand
{
    public function __invoke(string $command) : ConsoleCommandResult
    {
        $code = 1;
        $output = [];
        exec($command, $output, $code);

        return new ConsoleCommandResult($command, $code, implode(PHP_EOL, $output));
    }
}
