<?php

namespace Nxu\PhpToml\Lexer\Strings;

enum QuotationMarkResult
{
    case KeepReading;
    case EndOfString;
}
