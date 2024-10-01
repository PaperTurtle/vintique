<?php

use App\Models\User;

it('updates user profile information', function () {
    $user = User::factory()->create();
    $updatedData = [
        'name' => 'New Name',
        'email' => 'newemail@example.com',
    ];

    $this->actingAs($user)
        ->patch(route('profile.update', ['userID' => $user->id]), $updatedData)
        ->assertRedirect(route('profile.show', ['userID' => $user->id]))
        ->assertSessionHas('status', 'Profile updated successfully.');

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'name' => 'New Name',
        'email' => 'newemail@example.com',
    ]);
});

it('deletes the user account', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->delete(route('profile.destroy', ['userID' => $user->id]), ['password' => 'password'])
        ->assertRedirect('/')
        ->assertSessionHas('status');

    $this->assertDatabaseMissing('users', ['id' => $user->id]);
});
