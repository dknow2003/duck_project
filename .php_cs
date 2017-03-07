<?php

$header = <<<EOF
@author: Abraham Greyson <82011220@qq.com>
EOF;

$level = Symfony\CS\FixerInterface::PSR2_LEVEL;

$fixers = [
    'header_comment',
    'blankline_after_open_tag',
    'multiline_array_trailing_comma',
    'no_blank_lines_after_class_opening',
    'no_empty_lines_after_phpdocs',
    'phpdoc_no_package',
    'phpdoc_var_without_name',
    'remove_lines_between_uses',
    'return',
    'single_array_no_trailing_comma',
    'single_quote',
    'ternary_spaces',
    'trim_array_spaces',
    'unary_operators_spaces',
    'ordered_use',
    'short_array_syntax',
    'whitespacy_lines',
    'spaces_cast'
];

//Symfony\CS\Fixer\Contrib\HeaderCommentFixer::setHeader($header);

$finder = Symfony\CS\Finder\DefaultFinder::create()->in(__DIR__ . '/app');

return Symfony\CS\Config\Config::create()->level($level)->fixers($fixers)->finder($finder);
