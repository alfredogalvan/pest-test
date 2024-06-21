<?php

use App\Models\User;
use Illuminate\Support\Facades\Auth;


beforeEach(fn () => [
    $this->user = User::factory()->create(['password' => bcrypt('password')]),
]);

it('displays the login page', function () {
    $this->get('/login')
        ->assertStatus(200);
});

it('logs in with correct credentials', function () {
    $this->post('/login', [
        'email' => $this->user->email,
        'password' => 'password',
    ])->assertStatus(302)
        ->assertRedirect('/');;
    expect(Auth::check())->toBeTrue();
});


it('does not log in with incorrect credentials', function () {

    $this->post('/login', [
        'email' => $this->user->email,
        'password' => 'wrong-password',
    ])
        ->assertStatus(302)
        ->assertRedirect('/')
        ->assertSessionHasErrors('email');
    expect(Auth::check())->toBeFalse();

});

it('navigates as a logged in user', function () {
    $this->actingAs($this->user)->get('/')
        ->assertStatus(200);

    Auth::logout();

    $this->get('/')
        ->assertRedirect('/login');
});
