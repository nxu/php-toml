<?php

namespace Nxu\PhpToml\Lexer\Strings;

class StringLiteral
{
    public string $lexeme = '';

    public bool $isMultiline = false;

    public function __construct(
        public readonly int $line,
        public readonly bool $isLiteral,
    ) {
    }

    public function quotationMark(): string
    {
        return $this->isLiteral
            ? "'"
            : '"';
    }

    public function concat(string $lexeme): void
    {
        $this->lexeme .= $lexeme;
    }

    public function markAsMultiline(): void
    {
        $this->isMultiline = true;
    }
}
