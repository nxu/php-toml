<?php

namespace Nxu\PhpToml\Lexer\Concerns;

use Nxu\PhpToml\Exceptions\TomlParserException;
use Nxu\PhpToml\Lexer\Token;
use Nxu\PhpToml\Lexer\TokenType;

trait ScansStrings
{
    private function basicString(): Token
    {
        $literal = '';
        $multiline = false;
        $line = $this->line;

        while (! $this->isEof() && $char = $this->advance()) {
            if ($char == '"') {
                break;
            }

            if ($char == "\n" && ! $multiline) {
                TomlParserException::throw('Unexpected end of line. Expected end of string', $this->line);
            }

            if ($char == '\\') {
                // Escape sequence
                $literal .= $this->getEscapedStringSequence();

                continue;
            }

            // Any other character gets appended to the string literal
            $literal .= $char;
        }

        return new Token(TokenType::String, $literal, $line);
    }

    private function getEscapedStringSequence(): string
    {
        $next = $this->advance();

        switch ($next) {
            case 'b':
                return "\u{0008}";

            case 't':
                return "\t";

            case 'n':
                return "\n";

            case 'f':
                return "\f";

            case 'r':
                return "\r";

            case '"':
                return '"';

            case '\\':
                return '\\';

            case 'u':
            case 'U':
                $unicode = $this->advanceMultiple($next === 'u' ? 4 : 8);
                $hex = implode('', $unicode);
                $character = mb_chr(hexdec($hex));

                if ($character === false) {
                    TomlParserException::throw("Invalid Unicode scalar value '$hex'", $this->line);
                }

                return $character;

            default:
                TomlParserException::throw("Invalid escape sequence '\\$next'", $this->line);
        }
    }
}
