<?php


use Illuminate\Testing\Fluent\AssertableJson;

test('user_with_correct_credentials_can_login', function () {
    \App\Models\User::factory()->create([
        "email" => 'mamdouh@w88.test'
    ]);

    $response = $this->post('/api/login', [
        "email" => 'mamdouh@w88.test',
        "password" => 'password'
    ]);

    $response
        ->assertJson(fn (AssertableJson $json) =>$json->hasAll([
            'data.name', 'data.email', 'token'
        ]))
        ->assertStatus(200);
});
