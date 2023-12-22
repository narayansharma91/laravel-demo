<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

beforeEach(function () {
    $this->seed();
    $this->user = createUser();
});

test('Get Profile subscription details if not subscribed', function () {

    $response = $this->actingAs($this->user)
        ->get(route('profile.subscription'));
    expect($response)
        ->status()->toBe(200)
        ->content()->toBeJson()
        ->json()->toHaveKeys(['data', 'message']);
});
