<?php

namespace Nxu\PhpToml\Parser;

use DateTimeInterface;

readonly class KeyValuePair
{
    public function __construct(
        public string $key,
        public string|bool|int|float|array|DateTimeInterface $value,
    ) {
    }
}
