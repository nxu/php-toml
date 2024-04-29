<?php

namespace Nxu\PhpToml\Parser;

use Nxu\PhpToml\Exceptions\TomlParserException;
use Nxu\PhpToml\Lexer\Token;
use Nxu\PhpToml\Lexer\TokenType;

class Parser
{
    /** @var Token[] */
    private readonly array $tokens;

    private int $tokenCount;

    private int $current = 0;

    /** @param Token[] $tokens */
    public function __construct(array $tokens)
    {
        $this->tokens = $tokens;
        $this->tokenCount = count($tokens);
    }

    public function parse(): array
    {
        $tableParser = new TableParser();

        $config = [];
        $currentTable = null;

        while ($this->isNotEof()) {
            $token = $this->advance();

            switch ($token->type) {
                case TokenType::OpeningSquareBracket:
                    // Table definition
                    $currentTable = $tableParser->parse($this, $token);
                    break;

                case TokenType::Literal:
                case TokenType::String:
                    // Key
                    throw new \Exception('To be implemented');
                    break;

                case TokenType::NewLine:
                    // Ignore newlines normally
                    break;

                case TokenType::EqualSign:
                case TokenType::Dot:
                case TokenType::Comma:
                case TokenType::ClosingSquareBracket:
                    TomlParserException::throw("Unexpected token '$token->lexeme'", $token->line);

                case TokenType::EOF:
                    // EOF - finish processing
                    break 2;
            }
        }
Í
        return $config;
    }

    public function isEof(): bool
    {
        return $this->current >= $this->tokenCount;
    }

    public function isNotEof(): bool
    {
        return ! $this->isEof();
    }

    public function advance(): ?Token
    {
        if ($this->isEof()) {
            return null;
        }

        return $this->tokens[$this->current++] ?? null;
    }

    public function peek(): Token
    {
        return $this->tokens[$this->current];
    }
}
