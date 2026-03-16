<?php

use App\Models\User;
use function Pest\Laravel\get;
use function Pest\Laravel\actingAs;

/** @var \Tests\TestCase $this */


test('locale priority query param', function () {
    $response = $this->get('/?lang=es');
    expect(app()->getLocale())->toBe('es');
});

test('locale priority user preference', function () {
    $user = User::factory()->create(['locale' => 'es']);
    $this->actingAs($user);

    $response = $this->get('/');
    expect(app()->getLocale())->toBe('es');
});

test('locale priority cookie', function () {
    $response = $this->call('GET', '/', [], ['app_locale' => 'es']);
    expect(app()->getLocale())->toBe('es');
});

test('locale priority accept language', function () {
    $response = $this->withHeaders(['Accept-Language' => 'es'])->get('/');
    expect(app()->getLocale())->toBe('es');
});

test('locale fallback', function () {
    $response = $this->get('/');
    expect(app()->getLocale())->toBe('en');
});
