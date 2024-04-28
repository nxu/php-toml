<?php

namespace Nxu\PhpToml\Lexer;

readonly class Token
{
    public function __construct(
        public TokenType $type,
        public ?string $lexeme,
        public int $line,
    ) {
    }
}
