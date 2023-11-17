<?php

use App\Models\User;

it('belongs to a user', function () {
    $user = User::factory()->create();
    $userProfile = \App\Models\UserProfile::factory()->create(['user_id' => $user->id]);

    expect($userProfile->user)->toBeInstanceOf(User::class);
});


it('has correct fillable attributes', function () {
    $userProfile = new \App\Models\UserProfile();

    expect($userProfile->getFillable())->toBe([
        'user_id',
        'location',
        'bio',
        'contact_info',
        'profile_picture',
    ]);
});


it('can be created', function () {
    $userProfile = \App\Models\UserProfile::factory()->create([
        'location' => 'New York',
        'bio' => 'This is a bio.',
    ]);

    $this->assertDatabaseHas('user_profiles', [
        'location' => 'New York',
        'bio' => 'This is a bio.',
    ]);
});

it('can update attributes', function () {
    $userProfile = \App\Models\UserProfile::factory()->create();

    $userProfile->update(['location' => 'Los Angeles']);

    $this->assertDatabaseHas('user_profiles', [
        'id' => $userProfile->id,
        'location' => 'Los Angeles',
    ]);
});