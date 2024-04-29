<?php

use Nxu\PhpToml\Lexer\Lexer;
use Nxu\PhpToml\Lexer\TokenType;

it('scans literals', function () {
    $lexer = new Lexer('these are all literals');

    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(5);
    expect($tokens[0]->type)->toBe(TokenType::Literal);
    expect($tokens[0]->lexeme)->toBe('these');

    expect($tokens[1]->type)->toBe(TokenType::Literal);
    expect($tokens[1]->lexeme)->toBe('are');

    expect($tokens[2]->type)->toBe(TokenType::Literal);
    expect($tokens[2]->lexeme)->toBe('all');

    expect($tokens[3]->type)->toBe(TokenType::Literal);
    expect($tokens[3]->lexeme)->toBe('literals');
});
