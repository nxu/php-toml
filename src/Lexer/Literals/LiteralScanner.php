<?php

namespace Nxu\PhpToml\Lexer\Literals;

use Nxu\PhpToml\Lexer\Lexer;
use Nxu\PhpToml\Lexer\Token;
use Nxu\PhpToml\Lexer\TokenType;

readonly class LiteralScanner
{
    public function canStartLiteral(string $char): bool
    {
        return preg_match('/[A-Za-z0-9_+-]/', $char) === 1;
    }

    public function scan(Lexer $lexer, string $currentChar): Token
    {
        $literal = $currentChar;

        while ($lexer->isNotEof()) {
            $char = $lexer->peek();

            if (! $this->isAllowedInLiteral($char)) {
                return new Token(TokenType::Literal, $literal, $lexer->line);
            }

            $literal .= $char;

            $lexer->advance();
        }

        // EOF reached
        return new Token(TokenType::Literal, $literal, $lexer->line);
    }

    private function isAllowedInLiteral(string $char): bool
    {
        // Allow `:` and `.` inside literals
        // `:` is only accepted in time values, `.` is allowed in floats
        return preg_match('/[A-Za-z0-9_+:.-]/', $char) === 1;
    }
}
