<?php

namespace PainlessPHP\Package\Boilerplate\Core\Version;

class SemanticVersionNumber
{
    public function __construct(
        public readonly int $major,
        public readonly int|null $minor = 0,
        public readonly int|string|null $patch = 0,
    )
    {
    }

    public static function fromString(string $version) : self
    {
        return self::fromArray(explode('.', $version));
    }

    public static function fromArray(array $data) : self
    {
        return new self(...$data);
    }

    public function __toString() : string
    {
        return $this->toString();
    }

    public function toString(SemanticVersionPrecision $precision = SemanticVersionPrecision::Patch) : string
    {
        $version = $this->major;

        if($this->minor && $precision <= SemanticVersionPrecision::Minor) {
            $version .= ".$this->minor";
        }

        if($this->patch && $precision <= SemanticVersionPrecision::Patch) {
            $version .= ".$this->patch";
        }

        return $version;
    }
}
