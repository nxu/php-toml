<?php

use Nxu\PhpToml\Lexer\Lexer;
use Nxu\PhpToml\Lexer\TokenType;

it('can parse simple literal string', function () {
    $lexer = new Lexer("'This is a very basic string'");
    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe('This is a very basic string');
});

it('ignores escape sequences', function () {
    $lexer = new Lexer('\'This is a very \n basic string\'');
    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe('This is a very \n basic string');
});

it('processes multiline string with immediate newline', function () {
    $lexer = new Lexer(<<<'TOML'
'''
Hello world!'''
TOML
    );

    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe('Hello world!');
});

it('processes multiline string without immediate newline', function () {
    $lexer = new Lexer(<<<'TOML'
'''Hello world!'''
TOML
    );

    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe('Hello world!');
});

it('processes multiline string with newlines', function () {
    $lexer = new Lexer(<<<'TOML'
'''
Hello
world!
'''
TOML
    );

    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe("Hello\nworld!\n");
});

it('processes unescaped quotation marks in multiline strings', function () {
    $lexer = new Lexer('\'\'\'Here are fifteen quotation marks: """""""""""""""\'\'\'');

    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe('Here are fifteen quotation marks: """""""""""""""');
});

it('processes unescaped apostrophes in multiline strings', function () {
    $lexer = new Lexer("'''That's it!'''");

    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe("That's it!");
});
