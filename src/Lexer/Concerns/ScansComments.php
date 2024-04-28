<?php

namespace Nxu\PhpToml\Lexer\Concerns;

trait ScansComments
{
    private function comment(): void
    {
        while ($this->peek() != "\n" && ! $this->isEof()) {
            $this->advance();
        }
    }
}
