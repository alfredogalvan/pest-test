<?php

use App\Models\Post;
use App\Models\User;


beforeEach(fn () => [
    $this->user = User::factory()->create(),
    $this->post = Post::factory()->create(['user_id' => $this->user])
]);

it('displays the create post page', function () {
    $this->actingAs($this->user)
        ->get('/create-post')
        ->assertStatus(200);
});

it('creates a new post with valid data', function () {
    $postData = [
        'title' => 'Test Post',
        'content' => 'This is a test post.',
    ];

    $this->actingAs($this->user)
        ->post('/post/store', $postData)
        ->assertStatus(302)
        ->assertRedirect('/posts');

    expect(Post::where('title', 'Test Post')->exists())->toBeTrue();
});

it('does not create a new post with invalid data', function () {
    $postData = [
        'title' => '',
        'content' => '',
    ];

    $this->actingAs($this->user)
        ->post('/post/store', $postData)
        ->assertStatus(302)
        ->assertSessionHasErrors(['title', 'content']);
});

it('updates a post with valid data', function () {

    $postData = [
        'title' => 'Updated Post',
        'content' => 'This is an updated post.',
    ];

    $this->actingAs($this->user)
        ->put("/post/{$this->post->id}/update", $postData)
        ->assertStatus(302)
        ->assertRedirect("/post/{$this->post->id}");
    expect($this->post->fresh()->title)->toBe('Updated Post');
});

it('deletes a post', function () {

    $postCount = Post::all()->count();

    $this->actingAs($this->user)
        ->delete("/post/{$this->post->id}/delete")
        ->assertStatus(302)
        ->assertRedirect('/posts');


    expect(Post::all()->count())->toBe($postCount - 1);
});
