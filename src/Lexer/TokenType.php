<?php

namespace Nxu\PhpToml\Lexer;

enum TokenType
{
    case EqualSign;
    case Comma;
    case OpeningSquareBracket;
    case ClosingSquareBracket;
    case OpeningBrace;
    case ClosingBrace;
    case NewLine;
    case EOF;

    case Key;

    case Boolean;
    case String;
    case Integer;
    case Float;
    case OffsetDateTime;
    case DateTime;
    case LocalDate;
    case LocalTime;
}
