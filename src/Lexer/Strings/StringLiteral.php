<?php

namespace Nxu\PhpToml\Lexer\Strings;

class StringLiteral
{
    public string $literal = '';

    public bool $isMultiline = false;

    public function __construct(
        public readonly int $line
    ) {
    }

    public function concat(string $literal): void
    {
        $this->literal .= $literal;
    }

    public function markAsMultiline(): void
    {
        $this->isMultiline = true;
    }
}
