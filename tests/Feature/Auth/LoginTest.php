<?php

use App\Models\User;

test('guest can view the login page', function () {
    $this->get(route('login'))->assertOk();
});

test('authenticated user cannot access the login page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('login'))
        ->assertRedirect(route('dashboard'));
});

test('user can log in using email', function () {
    $user = User::factory()->create([
        'email' => 'auth@example.com',
        'password' => 'secret-password',
    ]);

    $response = $this->post(route('login.store'), [
        'login' => 'auth@example.com',
        'password' => 'secret-password',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('user can log in using username', function () {
    $user = User::factory()->create([
        'username' => 'mozo1',
        'password' => 'secret-password',
    ]);

    $response = $this->post(route('login.store'), [
        'login' => 'mozo1',
        'password' => 'secret-password',
    ]);

    $response->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);
});

test('user cannot log in with invalid credentials', function () {
    User::factory()->create([
        'email' => 'auth@example.com',
        'password' => 'secret-password',
    ]);

    $response = $this->from(route('login'))->post(route('login.store'), [
        'login' => 'auth@example.com',
        'password' => 'wrong-password',
    ]);

    $response->assertRedirect(route('login'));
    $response->assertSessionHasErrors('login');
    $this->assertGuest();
});

test('user can log out', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->post(route('logout'))
        ->assertRedirect(route('login'));

    $this->assertGuest();
});

test('dashboard requires authentication', function () {
    $this->get(route('dashboard'))->assertRedirect(route('login'));
});
