<?php

use Nxu\PhpToml\Exceptions\TomlParserException;
use Nxu\PhpToml\Lexer\Lexer;
use Nxu\PhpToml\Lexer\TokenType;

it('can parse simple basic string', function () {
    $lexer = new Lexer('"This is a very basic string"');
    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe('This is a very basic string');
});

it('can parse string with dots', function () {
    $lexer = new Lexer('"10.0.0.1"');
    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe('10.0.0.1');
});

it('can parse escape sequences', function () {
    $lexer = new Lexer('"Escapes \b \t \n \f \r \" \\\\ \u000A \U0000000A"');
    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe("Escapes \u{8} \t \n \f \r \" \\ \n \n");
});

it('ignores characters after Unicode escape sequence', function () {
    $lexer = new Lexer('"\u000AHello \U0000000AHello"');
    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe("\nHello \nHello");
});

it('processes multiline string with immediate newline', function () {
    $lexer = new Lexer(<<<'TOML'
"""
Hello world!"""
TOML
    );

    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe('Hello world!');
});

it('processes multiline string with immediate CRLF', function () {
    $lexer = new Lexer("\"\"\"\r\nHello world!\"\"\"");

    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe('Hello world!');
});

it('processes multiline string without immediate newline', function () {
    $lexer = new Lexer(<<<'TOML'
"""Hello world!"""
TOML
    );

    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe('Hello world!');
});

it('processes multiline string with newlines', function () {
    $lexer = new Lexer(<<<'TOML'
"""
Hello
world!
"""
TOML
    );

    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe("Hello\nworld!\n");
});

it('processes unescaped quotation marks in multiline strings', function () {
    $lexer = new Lexer('"""Here are fifteen quotation marks: ""\\"""\\"""\\"""\\"""\\"."""');

    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0]->type)->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe('Here are fifteen quotation marks: """"""""""""""".');
});

it('handles line ending backslashes', function () {
    $lexer = new Lexer(<<<TOML
"""
The quick brown \


  fox jumps over \
    the lazy dog."""
TOML
    );

    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(2);
    expect($tokens[0])->type->toBe(TokenType::String);
    expect($tokens[0]->lexeme)->toBe('The quick brown fox jumps over the lazy dog.');
});

it('throws exception for invalid escape sequences', function () {
    (new Lexer('"\x"'))->scan();
})->expectException(TomlParserException::class);

it('throws exception for invalid escaped Unicode scalar values', function () {
    (new Lexer('"\U0011FFFF"'))->scan();
})->expectException(TomlParserException::class);

it('throws exception when EOL reached within a single line string', function () {
    (new Lexer("\"This is a single line string with no ending quotes\n"))->scan();
})->expectException(TomlParserException::class);

it('throws exception when EOF reached within a multiline string', function () {
    (new Lexer('"""This is a multiline string with no ending quotes'))->scan();
})->expectException(TomlParserException::class);
