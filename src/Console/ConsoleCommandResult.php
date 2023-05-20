<?php

namespace PainlessPHP\Package\Boilerplate\Core\Console;

class ConsoleCommandResult
{
    public function __construct(
        private string $command,
        private int $code,
        private string $output
    )
    {
    }

    public function getCommand() : string
    {
        return $this->command;
    }

    public function getCode() : int
    {
        return $this->code;
    }

    public function getOutput() : string
    {
        return $this->output;
    }

    public function ranSuccessfully() : bool
    {
        return $this->code = 0;
    }
}
