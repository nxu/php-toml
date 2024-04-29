<?php

use Nxu\PhpToml\Lexer\Lexer;
use Nxu\PhpToml\Lexer\TokenType;

it('ignores whitespace and comments', function () {
    $toml = <<<TOML
# Hello, World!
\t    \t  # End of line comment
TOML;

    $lexer = new Lexer($toml);
    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::NewLine);
    expect($tokens[1]->type)->toBe(TokenType::EOF);
});

it('parses equal sign', function () {
    $toml = <<<TOML
\t=#This is a comment
TOML;

    $lexer = new Lexer($toml);
    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::EqualSign);
});

it('parses square backets', function () {
    $toml = <<<'TOML'
[[]]
TOML;

    $lexer = new Lexer($toml);
    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(5);
    expect($tokens[0]->type)->toBe(TokenType::OpeningSquareBracket);
    expect($tokens[1]->type)->toBe(TokenType::OpeningSquareBracket);
    expect($tokens[2]->type)->toBe(TokenType::ClosingSquareBracket);
    expect($tokens[3]->type)->toBe(TokenType::ClosingSquareBracket);
});

it('parses braces', function () {
    $toml = <<<'TOML'
{}
TOML;

    $lexer = new Lexer($toml);
    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(3);
    expect($tokens[0]->type)->toBe(TokenType::OpeningBrace);
    expect($tokens[1]->type)->toBe(TokenType::ClosingBrace);
});

it('parses commas and dots', function () {
    $toml = <<<'TOML'
,.
TOML;

    $lexer = new Lexer($toml);
    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(3);
    expect($tokens[0]->type)->toBe(TokenType::Comma);
    expect($tokens[1]->type)->toBe(TokenType::Dot);
});
