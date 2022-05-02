<?php

return [
    [
        1234567.89,
        '1,234,567.890',
    ],
    [
        1234567.89,
        '1 234 567,890', ',', ' ',
    ],
    [
        -1234567.89,
        '-1 234 567,890', ',', ' ',
    ],
    [
        '#VALUE!',
        '1 234 567,890-', ',', ' ',
    ],
    [
        '#VALUE!',
        '1,234,567.890,123',
    ],
    [
        '#VALUE!',
        '1.234.567.890,123',
    ],
    [
        1234567.890,
        '1.234.567,890', ',', '.',
    ],
    [
        '#VALUE!',
        '1.234.567,89',
    ],
    [
        12345.6789,
        '1,234,567.89%',
    ],
    [
        123.456789,
        '1,234,567.89%%',
    ],
    [
        1.23456789,
        '1,234,567.89%%%',
    ],
    [
        '#VALUE!',
        '1,234,567.89-%',
    ],
    'no arguments' => ['exception'],
    'boolean argument' => ['#VALUE!', true],
];
