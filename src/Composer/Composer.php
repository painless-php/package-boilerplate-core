<?php

namespace PainlessPHP\Package\Boilerplate\Core\Composer;

use Exception;
use PainlessPHP\Package\Boilerplate\Core\Helpers;

class Composer
{
    const FILENAME = 'composer.json';

    private string $filepath;
    private array $data;

    public function __construct(string $filepath)
    {
        $this->filepath = $filepath;
        $this->data = json_decode(file_get_contents($filepath), true, JSON_THROW_ON_ERROR);
    }

    public static function local() : self
    {
        return new self(self::locateJson());
    }

    public static function locateJson(string $path = __DIR__) : string
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

        return self::locateJson("$parentDirPath/composer.json");
    }

    public function toArray() : array
    {
        return $this->data;
    }

    public function edit(callable $callback, string|null $key = null, string $nestingIndicator = null, int $jsonWriteFlags = 0, bool $autoWrite = false) : self
    {
        $data = Helpers::arrayGetNested($this->data, $key, $nestingIndicator);
        $data = $callback($data);
        $this->data = Helpers::arraySetNested($this->data, $data, $key, $nestingIndicator);

        if($autoWrite) {
            $this->write($jsonWriteFlags);
        }

        return $this;
    }

    public function write(string $filepath = null, int $jsonWriteFlags = 0)
    {
        if($filepath === null) {
            $filepath = $this->filepath;
        }

        file_put_contents($filepath, json_encode($this->data, $jsonWriteFlags));
    }
}
