<?php

declare(strict_types=1);

use Josecl\PhpLibraryBoilerplate\Example;

test('example', function () {
    expect(true)->toBeTrue();
});

test('add', function () {
    $example = new Example();
    expect($example->add(1, 2))->toBe(3);
});
