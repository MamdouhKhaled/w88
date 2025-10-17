<?php

use App\Models\Bookmark;
use App\Models\User;
use Illuminate\Testing\Fluent\AssertableJson;
test('un authorize user can\'n create bookmark', function () {
    $response = $this->postJson('/api/bookmarks', [
        "url" => "https://google.com",
        "title" => "Test bookmark",
        "note" => "Test note",
    ]);
    $response->assertExactJson([
        'message' => 'Unauthenticated.',
    ])->assertUnauthorized();
});
// create
test('auth user can create bookmark', function () {
    \Laravel\Sanctum\Sanctum::actingAs(
        User::factory()->create(),
        ['create']
    );

    $response = $this->postJson('/api/bookmarks', [
        "url" => "https://google.com",
        "title" => "Test bookmark",
        "note" => "Test note",
    ]);

    $response
        ->assertJson(fn (AssertableJson $json) =>$json->hasAll([
            'data.url',
            'data.title',
            'data.note',
            'message','status'
        ]))
        ->assertStatus(201);
});
// update
test('auth user can update bookmark', function () {
    $user = User::factory()->create();
    \Laravel\Sanctum\Sanctum::actingAs(
        $user,
        ['create']
    );

    $bookmark = Bookmark::factory()->create(
        ['user_id' => $user->id]
    );

    $response = $this->putJson('/api/bookmarks/'.$bookmark->id, [
        "url" => "https://google.com",
        "title" => "Test bookmark",
        "note" => "Test note",
        "tags" => ['search']
    ]);

    $response
        ->assertJson(fn (AssertableJson $json) =>$json->hasAll([
            'data.url',
            'data.title',
            'data.note',
            'message','status'
        ]))
        ->assertStatus(200);
    $this->assertDatabaseHas('bookmarks', [
        'id' => $bookmark->id,
        'url' => 'https://google.com',
    ]);
});
// delete
test('auth user can delete bookmark', function () {
    $user = User::factory()->create();
    \Laravel\Sanctum\Sanctum::actingAs(
        $user,
        ['create']
    );

    $bookmark = Bookmark::factory()->create(
        ['user_id' => $user->id]
    );
    $this->assertDatabaseCount(Bookmark::class, 1);
    $response = $this->DeleteJson('/api/bookmarks/'.$bookmark->id, [
        "url" => "https://google.com",
        "title" => "Test bookmark",
        "note" => "Test note",
        "tags" => ['search']
    ]);

    $response
        ->assertStatus(200);
    $this->assertDatabaseCount(Bookmark::class, 0);
});
// share
test('auth user can share bookmark', function () {
    $user = User::factory()->create();
    \Laravel\Sanctum\Sanctum::actingAs(
        $user,
        ['create']
    );

    $bookmark = Bookmark::factory()->create(
        ['user_id' => $user->id]
    );

    $response = $this->putJson('/api/bookmarks/'.$bookmark->id.'/share', []);

    $response
        ->assertJson(fn (AssertableJson $json) =>$json->hasAll([
            'data.url',
            'data.title',
            'data.note',
            'data.tags',
            'data.share_token',
            'message','status'
        ]))
        ->assertStatus(200);
});


test('user one can\'t show bookmark for user two', function () {
    $users = User::factory(2)->create();
    \Laravel\Sanctum\Sanctum::actingAs(
        $users->first(),
        ['create']
    );

    $bookmark = Bookmark::factory()->create(
        ['user_id' => $users->last()->id]
    );

    $response = $this->getJson('/api/bookmarks/'.$bookmark->id);

    $response
        ->assertStatus(403);
});


