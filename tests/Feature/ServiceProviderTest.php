<?php

use AgenticMorf\FluxUILicensing\Tests\TestCase;

uses(TestCase::class);

it('merges default config', function () {
    expect(config('fluxui-licensing.route'))->toBe('settings/licenses');
    expect(config('fluxui-licensing.route_name'))->toBe('licensing.index');
    expect(config('fluxui-licensing.per_page'))->toBe(15);
});
