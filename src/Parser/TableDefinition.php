<?php

namespace Nxu\PhpToml\Parser;

class TableDefinition
{
    public string $name = '';

    public bool $isArray = false;

    public function __construct(public readonly int $line)
    {
    }

    public function appendToName(string $name): void
    {
        $this->name .= $name;
    }

    public function markAsArray(): void
    {
        $this->isArray = true;
    }
}
