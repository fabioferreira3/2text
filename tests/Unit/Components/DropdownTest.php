<?php

use App\View\Components\Custom\Dropdown;

it('renders the component view correctly', function () {
    $view = (new Dropdown())->render();
    expect($view->getName())->toBe('components.custom.dropdown');
    expect($view->getData())->toBeArray();
});


it('renders the default state correctly', function () {
    $component = new Dropdown();

    expect($component->width)->toBe('w-48')
        ->and($component->height)->toBe('max-h-96')
        ->and($component->align)->toBe(Dropdown::DEFAULT_ALIGN)
        ->and($component->persistent)->toBeFalse()
        ->and($component->trigger)->toBeNull()
        ->and($component->direction)->toBe('down');
});

it('can set custom properties', function () {
    $component = new Dropdown('w-24', 'max-h-48', 'left', true, 'click', 'up');

    expect($component->width)->toBe('w-24')
        ->and($component->height)->toBe('max-h-48')
        ->and($component->align)->toBe('left')
        ->and($component->persistent)->toBeTrue()
        ->and($component->trigger)->toBe('click')
        ->and($component->direction)->toBe('up');
});

it('returns the correct alignment class for right alignment', function () {
    $component = new Dropdown(align: 'right');
    expect($component->getAlign())->toBe('origin-top-right right-0');
});

it('returns the correct alignment class for left alignment', function () {
    $component = new Dropdown(align: 'left');
    expect($component->getAlign())->toBe('origin-top-left left-0');
});

it('returns the correct alignment class for top-right alignment', function () {
    $component = new Dropdown(align: 'top-right');
    expect($component->getAlign())->toBe('origin-top-right right-0 bottom-0');
});

it('returns the correct alignment class for top-left alignment', function () {
    $component = new Dropdown(align: 'top-left');
    expect($component->getAlign())->toBe('origin-top-left left-0 bottom-0');
});

it('throws an exception for invalid alignments', function () {
    $component = new Dropdown(align: 'invalid');
    $component->getAlign();
})->throws(ErrorException::class);
