<?php

namespace Nxu\PhpToml\Lexer;

use Nxu\PhpToml\Lexer\Strings\StringScanner;

class Lexer
{
    /** @var string[] */
    private array $source;

    private int $sourceLength;

    private int $current = 0;

    public int $line = 1;

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

        while ($this->isNotEof() && ($char = $this->advance())) {
            switch ($char) {
                case '"':
                    // Parse string literal
                    $tokens[] = (new StringScanner())->scan($this);
                    break;

                case '#':
                    // Advance until newline or EOF and ignore comment
                    while ($this->peek() != "\n" && ! $this->isEof()) {
                        $this->advance();
                    }
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

    public function isEof(): bool
    {
        return $this->current >= $this->sourceLength;
    }

    public function isNotEof(): bool
    {
        return ! $this->isEof();
    }

    public function isWhitespace(string $char): bool
    {
        return $char == ' ' || $char == "\t" || $char == "\n" || $char == "\r";
    }

    public function advance(): string
    {
        return $this->source[$this->current++];
    }

    /**
     * @return array<int, string>
     */
    public function advanceMultiple(int $count): array
    {
        $characters = [];

        for ($i = 0; $i < $count; $i++) {
            $characters[] = $this->advance();
        }

        return $characters;
    }

    public function peek(): string
    {
        if ($this->isEof()) {
            return "\0";
        }

        return $this->source[$this->current];
    }
}
