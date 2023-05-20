<?php

namespace PainlessPHP\Package\Boilerplate\Core\Version;

enum SemanticVersionPrecision : int
{
    case Major = 1;
    case Minor = 2;
    case Patch = 3;
}
