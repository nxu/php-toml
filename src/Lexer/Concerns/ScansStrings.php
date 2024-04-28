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
                if ($multiline) {
                    // Already in a multiline string, process end of string
                    if ($this->advanceMultiple(2) != ['"', '"']) {
                        TomlParserException::throw("Unexpected '\"' - expected end of multiline string ('\"\"\"'')", $this->line);
                    }

                    break;
                } elseif ($this->isMultiline()) {
                    // Check if it is a start of a multiline string
                    $multiline = true;

                    continue;
                }
                // Else = end of string
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

    private function isMultiline(): bool
    {
        // Starting and second " has been processed, check if there is a third
        if ($this->peek() != '"') {
            return false;
        }

        // Ignore third quotation mark
        $this->advance();

        // Ignore immediate newline
        if ($this->peek() == "\r") {
            $this->advance();
        }

        if ($this->peek() == "\n") {
            $this->advance();
        }

        return true;
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
