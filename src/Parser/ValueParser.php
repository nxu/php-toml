<?php

namespace Nxu\PhpToml\Parser;

use DateTimeInterface;
use Nxu\PhpToml\Exceptions\TomlParserException;
use Nxu\PhpToml\Lexer\Token;
use Nxu\PhpToml\Lexer\TokenType;

class ValueParser
{
    public function parse(Parser $parser, Token $startingToken): KeyValuePair
    {
        $key = $this->parseKey($parser, $startingToken);
        $value = null;

        while ($parser->isNotEof()) {
            $token = $parser->advance();

            switch ($token->type) {
                case TokenType::OpeningSquareBracket:
                    throw new \Exception('To be implemented');
                    break;

                case TokenType::OpeningBrace:
                    throw new \Exception('To be implemented');
                    break;

                case TokenType::String:
                    $value = $token->lexeme;
                    break 2;

                case TokenType::Literal:


                default:
                    TomlParserException::throw("Unexpected value '$token->lexeme'", $token->line);
            }
        }

        return new KeyValuePair($key, $value);
    }

    private function parseKey(Parser $parser, Token $startingToken): string
    {
        $key = $startingToken->lexeme;

        while  ($parser->isNotEof()) {
            $token = $parser->advance();

            switch ($token->type) {
                case TokenType::EqualSign:
                    // End of key reached - return immediately
                    break 2;

                case TokenType::String:
                case TokenType::Literal:
                case TokenType::Dot:
                    $key .= $token->lexeme;
                    break;

                default:
                    TomlParserException::throw("Unexpected token '$token->lexeme'", $token->line);
            }
        }

        return $key;
    }
}
