<?php

namespace Nxu\PhpToml\Lexer;

enum TokenType
{
    case EqualSign;
    case Dot;
    case Comma;
    case OpeningSquareBracket;
    case ClosingSquareBracket;
    case OpeningBrace;
    case ClosingBrace;
    case NewLine;
    case String;
    case Literal;
    case EOF;
}
