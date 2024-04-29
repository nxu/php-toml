<?php

namespace Nxu\PhpToml\Lexer\Strings;

use Nxu\PhpToml\Exceptions\TomlParserException;
use Nxu\PhpToml\Lexer\Lexer;
use Nxu\PhpToml\Lexer\Token;
use Nxu\PhpToml\Lexer\TokenType;

readonly class StringScanner
{
    public function scan(Lexer $lexer, bool $isLiteral): Token
    {
        $string = new StringLiteral($lexer->line, isLiteral: $isLiteral);

        while ($lexer->isNotEof()) {
            $char = $lexer->advance();

            if ($string->isMultiline && $lexer->isEof()) {
                // Multiline strings must be closed with """
                TomlParserException::throw('Unexpected end of file. Expected end of multiline string (\'"""\')', $lexer->line);
            }

            $result = $this->handleCharacter($lexer, $string, $char);

            if ($result == StringReadingResult::EndOfString) {
                break;
            }
        }

        return new Token(TokenType::String, $string->lexeme, $string->line);
    }

    private function handleCharacter(Lexer $lexer, StringLiteral $string, string $char): StringReadingResult
    {
        if ($char == $string->quotationMark()) {
            return $this->handleQuotationMark($lexer, $string);
        }

        if ($char == '\\' && ! $string->isLiteral) {
            $this->handleBackslash($lexer, $string);

            return StringReadingResult::KeepReading;
        }

        if ($char == "\n" && $string->isMultiline) {
            // Newlines in multiline strings are read as-is
            $lexer->line++;
        } elseif ($char == "\n") {
            TomlParserException::throw('Unexpected end of line. Expected end of string', $lexer->line);
        }

        // Any other character gets appended to the string literal
        $string->concat($char);

        return StringReadingResult::KeepReading;
    }

    private function handleQuotationMark(Lexer $lexer, StringLiteral $string): StringReadingResult
    {
        if ($string->isMultiline) {
            // Quotation marks are allowed in multiline strings. They can
            // - either be quotation mark literals (" or "")
            // - or mark the end of multiline strings (""")
            return $this->handleQuotationMarkInMultilineString($lexer, $string);
        } elseif ($this->isStartOfMultilineString($lexer, $string)) {
            $string->markAsMultiline();

            return StringReadingResult::KeepReading;
        }

        // Else = end of string
        return StringReadingResult::EndOfString;
    }

    private function handleQuotationMarkInMultilineString(Lexer $lexer, StringLiteral $string): StringReadingResult
    {
        $next = $lexer->advance();

        if ($next == $string->quotationMark() && $lexer->peek() == $string->quotationMark()) {
            // End of multiline string
            $lexer->advance();

            return StringReadingResult::EndOfString;
        }

        // Add the first quotation mark we checked
        $string->concat($string->quotationMark());

        // Add the next character
        $string->concat($next);

        return StringReadingResult::KeepReading;
    }

    private function isStartOfMultilineString(Lexer $lexer, StringLiteral $string): bool
    {
        // Starting and second quotation mark has been processed, check if there is a third
        if ($lexer->peek() != $string->quotationMark()) {
            return false;
        }

        // Ignore third quotation mark
        $lexer->advance();

        // Ignore immediate newline (LF / CRLF)
        if ($lexer->peek() == "\r") {
            $lexer->advance();
        }

        if ($lexer->peek() == "\n") {
            $lexer->advance();
        }

        return true;
    }

    private function handleBackslash(Lexer $lexer, StringLiteral $string): void
    {
        if ($string->isMultiline && in_array($lexer->peek(), ["\r", "\n"])) {
            // Line ending backslash
            $this->handleLineEndingBackslash($lexer);
        } else {
            // Escape sequence
            $string->concat($this->getEscapedStringSequence($lexer));
        }
    }

    private function handleLineEndingBackslash(Lexer $lexer)
    {
        // Ignore all consecutive whitespaces
        while ($lexer->isNotEof() && $lexer->isWhitespace($lexer->peek())) {
            $lexer->advance();
        }
    }

    private function getEscapedStringSequence(Lexer $lexer): string
    {
        $next = $lexer->advance();

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
                $unicode = $lexer->advanceMultiple($next === 'u' ? 4 : 8);
                $hex = implode('', $unicode);
                $character = mb_chr(hexdec($hex));

                if ($character === false) {
                    TomlParserException::throw("Invalid Unicode scalar value '$hex'", $lexer->line);
                }

                return $character;

            default:
                TomlParserException::throw("Invalid escape sequence '\\$next'", $lexer->line);
        }
    }
}
