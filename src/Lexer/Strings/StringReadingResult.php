<?php

namespace Nxu\PhpToml\Lexer\Strings;

enum StringReadingResult
{
    case KeepReading;
    case EndOfString;
}
