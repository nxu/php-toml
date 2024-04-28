<?php

namespace Nxu\PhpToml\Lexer;

use Nxu\PhpToml\Lexer\Concerns\ScansComments;
use Nxu\PhpToml\Lexer\Concerns\ScansStrings;

class Lexer
{
    use ScansComments;
    use ScansStrings;

    /** @var string[] */
    private array $source;

    private int $sourceLength;

    private int $start = 0;

    private int $current = 0;

    private int $line = 1;

    public function __construct(string $source)
    {
        $this->source = mb_str_split($source, 1, 'UTF-8');
        $this->sourceLength = count($this->source);
    }

    /**
     * @return Token[]
     */
    public function scan(): array
    {
        $tokens = [];

        while (! $this->isEof() && ($char = $this->advance())) {
            switch ($char) {
                case '"':
                    $tokens[] = $this->basicString();
                    break;

                case '#':
                    $this->comment();
                    break;

                case '=':
                    $tokens[] = new Token(TokenType::EqualSign, $char, $this->line);
                    break;

                case "\n":
                    $tokens[] = new Token(TokenType::NewLine, $char, $this->line++);
                    break;

                case '[':
                    $tokens[] = new Token(TokenType::OpeningSquareBracket, $char, $this->line);
                    break;

                case ']':
                    $tokens[] = new Token(TokenType::ClosingSquareBracket, $char, $this->line);
                    break;

                case ',':
                    $tokens[] = new Token(TokenType::Comma, $char, $this->line);
                    break;

                case '{':
                    $tokens[] = new Token(TokenType::OpeningBrace, $char, $this->line);
                    break;

                case '}':
                    $tokens[] = new Token(TokenType::ClosingBrace, $char, $this->line);
                    break;

                case ' ':
                case "\r":
                case "\t":
                    break;
            }
        }

        $tokens[] = new Token(TokenType::EOF, null, $this->line);

        return $tokens;
    }

    private function isEof(): bool
    {
        return $this->current >= $this->sourceLength;
    }

    private function advance(): string
    {
        return $this->source[$this->current++];
    }

    /**
     * @return array<int, string>
     */
    private function advanceMultiple(int $count): array
    {
        $characters = [];

        for ($i = 0; $i < $count; $i++) {
            $characters[] = $this->advance();
        }

        return $characters;
    }

    private function peek(): string
    {
        if ($this->isEof()) {
            return "\0";
        }

        return $this->source[$this->current];
    }
}
