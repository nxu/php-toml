<?php

use Nxu\PhpToml\Lexer\Lexer;
use Nxu\PhpToml\Lexer\TokenType;

it('scans TOML', function () {
    $lexer = new Lexer(file_get_contents(__DIR__.'/../../example.toml'));

    $tokens = $lexer->scan();

    expect($tokens)->toHaveCount(79)
        // Comment and empty row
        ->and($tokens[0]->type)->toBe(TokenType::NewLine)
        ->and($tokens[1]->type)->toBe(TokenType::NewLine)

        // title = "TOML Example"
        ->and($tokens[2]->type)->toBe(TokenType::Literal)
        ->and($tokens[2]->lexeme)->toBe('title')
        ->and($tokens[3]->type)->toBe(TokenType::EqualSign)
        ->and($tokens[4]->type)->toBe(TokenType::String)
        ->and($tokens[4]->lexeme)->toBe('TOML Example')
        ->and($tokens[5]->type)->toBe(TokenType::NewLine)
        ->and($tokens[6]->type)->toBe(TokenType::NewLine)

        // [owner]
        ->and($tokens[7]->type)->toBe(TokenType::OpeningSquareBracket)
        ->and($tokens[8]->type)->toBe(TokenType::Literal)
        ->and($tokens[8]->lexeme)->toBe('owner')
        ->and($tokens[9]->type)->toBe(TokenType::ClosingSquareBracket)
        ->and($tokens[10]->type)->toBe(TokenType::NewLine)

        // name = "Tom Preston-Werner"
        ->and($tokens[11]->type)->toBe(TokenType::Literal)
        ->and($tokens[11]->lexeme)->toBe('name')
        ->and($tokens[12]->type)->toBe(TokenType::EqualSign)
        ->and($tokens[13]->type)->toBe(TokenType::String)
        ->and($tokens[13]->lexeme)->toBe('Tom Preston-Werner')
        ->and($tokens[14]->type)->toBe(TokenType::NewLine)

        // dob = 1979-05-27T07:32:00-08:00
        ->and($tokens[15]->type)->toBe(TokenType::Literal)
        ->and($tokens[15]->lexeme)->toBe('dob')
        ->and($tokens[16]->type)->toBe(TokenType::EqualSign)
        ->and($tokens[17]->type)->toBe(TokenType::Literal)
        ->and($tokens[17]->lexeme)->toBe('1979-05-27T07:32:00-08:00')
        ->and($tokens[18]->type)->toBe(TokenType::NewLine)
        ->and($tokens[19]->type)->toBe(TokenType::NewLine)

        // [database]
        ->and($tokens[20]->type)->toBe(TokenType::OpeningSquareBracket)
        ->and($tokens[21]->type)->toBe(TokenType::Literal)
        ->and($tokens[21]->lexeme)->toBe('database')
        ->and($tokens[22]->type)->toBe(TokenType::ClosingSquareBracket)
        ->and($tokens[23]->type)->toBe(TokenType::NewLine)

        // enabled = true
        ->and($tokens[24]->type)->toBe(TokenType::Literal)
        ->and($tokens[24]->lexeme)->toBe('enabled')
        ->and($tokens[25]->type)->toBe(TokenType::EqualSign)
        ->and($tokens[26]->type)->toBe(TokenType::Literal)
        ->and($tokens[26]->lexeme)->toBe('true')
        ->and($tokens[27]->type)->toBe(TokenType::NewLine)

        // ports = [ 8000, 8001, 8002 ]
        ->and($tokens[28]->type)->toBe(TokenType::Literal)
        ->and($tokens[28]->lexeme)->toBe('ports')
        ->and($tokens[29]->type)->toBe(TokenType::EqualSign)
        ->and($tokens[30]->type)->toBe(TokenType::OpeningSquareBracket)
        ->and($tokens[31]->type)->toBe(TokenType::Literal)
        ->and($tokens[31]->lexeme)->toBe('8000')
        ->and($tokens[32]->type)->toBe(TokenType::Comma)
        ->and($tokens[33]->type)->toBe(TokenType::Literal)
        ->and($tokens[33]->lexeme)->toBe('8001')
        ->and($tokens[34]->type)->toBe(TokenType::Comma)
        ->and($tokens[35]->type)->toBe(TokenType::Literal)
        ->and($tokens[35]->lexeme)->toBe('8002')
        ->and($tokens[36]->type)->toBe(TokenType::ClosingSquareBracket)
        ->and($tokens[37]->type)->toBe(TokenType::NewLine)

        // data = [ ["delta", "phi"], [3.14] ]
        ->and($tokens[38]->type)->toBe(TokenType::Literal)
        ->and($tokens[38]->lexeme)->toBe('data')
        ->and($tokens[39]->type)->toBe(TokenType::EqualSign)
        ->and($tokens[40]->type)->toBe(TokenType::OpeningSquareBracket)
        ->and($tokens[41]->type)->toBe(TokenType::OpeningSquareBracket)
        ->and($tokens[42]->type)->toBe(TokenType::String)
        ->and($tokens[42]->lexeme)->toBe('delta')
        ->and($tokens[43]->type)->toBe(TokenType::Comma)
        ->and($tokens[44]->type)->toBe(TokenType::String)
        ->and($tokens[44]->lexeme)->toBe('phi')
        ->and($tokens[45]->type)->toBe(TokenType::ClosingSquareBracket)
        ->and($tokens[46]->type)->toBe(TokenType::Comma)
        ->and($tokens[47]->type)->toBe(TokenType::OpeningSquareBracket)
        ->and($tokens[48]->type)->toBe(TokenType::Literal)
        ->and($tokens[48]->lexeme)->toBe('3.14')
        ->and($tokens[49]->type)->toBe(TokenType::ClosingSquareBracket)
        ->and($tokens[50]->type)->toBe(TokenType::ClosingSquareBracket)
        ->and($tokens[51]->type)->toBe(TokenType::NewLine)

        // temp_targets = { cpu = 79.5, case = 72.0 }
        ->and($tokens[52]->type)->toBe(TokenType::Literal)
        ->and($tokens[52]->lexeme)->toBe('temp_targets')
        ->and($tokens[53]->type)->toBe(TokenType::EqualSign)
        ->and($tokens[54]->type)->toBe(TokenType::OpeningBrace)
        ->and($tokens[55]->type)->toBe(TokenType::Literal)
        ->and($tokens[55]->lexeme)->toBe('cpu')
        ->and($tokens[56]->type)->toBe(TokenType::EqualSign)
        ->and($tokens[57]->type)->toBe(TokenType::Literal)
        ->and($tokens[57]->lexeme)->toBe('79.5')
        ->and($tokens[58]->type)->toBe(TokenType::Comma)
        ->and($tokens[59]->type)->toBe(TokenType::Literal)
        ->and($tokens[59]->lexeme)->toBe('case')
        ->and($tokens[60]->type)->toBe(TokenType::EqualSign)
        ->and($tokens[61]->type)->toBe(TokenType::Literal)
        ->and($tokens[61]->lexeme)->toBe('72.0')
        ->and($tokens[62]->type)->toBe(TokenType::ClosingBrace)
        ->and($tokens[63]->type)->toBe(TokenType::NewLine)
        ->and($tokens[64]->type)->toBe(TokenType::NewLine)

        // [servers]
        ->and($tokens[65]->type)->toBe(TokenType::OpeningSquareBracket)
        ->and($tokens[66]->type)->toBe(TokenType::Literal)
        ->and($tokens[66]->lexeme)->toBe('servers')
        ->and($tokens[67]->type)->toBe(TokenType::ClosingSquareBracket)
        ->and($tokens[68]->type)->toBe(TokenType::NewLine)
        ->and($tokens[69]->type)->toBe(TokenType::NewLine)

        // [servers.alpha]
        ->and($tokens[70]->type)->toBe(TokenType::OpeningSquareBracket)
        ->and($tokens[71]->type)->toBe(TokenType::Literal)
        ->and($tokens[71]->lexeme)->toBe('servers.alpha')
        ->and($tokens[72]->type)->toBe(TokenType::ClosingSquareBracket)
        ->and($tokens[73]->type)->toBe(TokenType::NewLine)

        // ip = "10.0.0.1"
        ->and($tokens[74]->type)->toBe(TokenType::Literal)
        ->and($tokens[74]->lexeme)->toBe('ip')
        ->and($tokens[75]->type)->toBe(TokenType::EqualSign)
        ->and($tokens[76]->type)->toBe(TokenType::String)
        ->and($tokens[76]->lexeme)->toBe('10.0.0.1')
        ->and($tokens[77]->type)->toBe(TokenType::NewLine)

        // EOF
        ->and($tokens[78]->type)->toBe(TokenType::EOF);
});
