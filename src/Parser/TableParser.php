<?php

namespace Nxu\PhpToml\Parser;

use Nxu\PhpToml\Exceptions\TomlParserException;
use Nxu\PhpToml\Lexer\Token;
use Nxu\PhpToml\Lexer\TokenType;

readonly class TableParser
{
    public function parse(Parser $parser, Token $startingToken): TableDefinition
    {
        $table = new TableDefinition($startingToken->line);

        while ($parser->isNotEof()) {
            $token = $parser->advance();

            switch ($token->type) {
                case TokenType::ClosingSquareBracket:
                    $this->handleClosingSquareBracket($parser, $table);
                    break 2;

                case TokenType::OpeningSquareBracket:
                    $this->handleOpeningSquareBracket($table);
                    break;

                case TokenType::String:
                case TokenType::Literal:
                case TokenType::Dot:
                    $table->appendToName($token->lexeme);
                    break;

                default:
                    TomlParserException::throw("Unexpected '$token->lexeme' in table definition", $token->line);
            }
        }

        if ($table->name === '') {
            TomlParserException::throw('Table name must not be empty', $startingToken->line);
        }

        return $table;
    }

    private function handleClosingSquareBracket(Parser $parser, TableDefinition $table): void
    {
        if (! $table->isArray) {
            // Table is not an array, close table definition
            return;
        }

        if ($parser->isEof()) {
            TomlParserException::throw("Unexpected EOF - expecting ']'", $table->line);
        }

        $next = $parser->advance();

        if ($next->type != TokenType::ClosingSquareBracket) {
            TomlParserException::throw("Unexpected '$next->lexeme' - expecting ']'", $table->line);
        }
    }

    private function handleOpeningSquareBracket(TableDefinition $table): void
    {
        if ($table->isArray) {
            TomlParserException::throw("Unexpected '[' - expecting name of table array", $table->line);
        }

        $table->markAsArray();
    }
}
