<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Store\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function testUsersCanLogin()
    {
        $user = factory(User::class)->create(['email' => 'user@test.com', 'password' => Hash::make('secret')]);
        $response = $this->post('/login', [
            'email' => 'user@test.com',
            'password' => 'secret',
        ]);

        $response->assertRedirect('/home');
        $this->assertAuthenticatedAs($user);
    }

    /** @test */
    public function testUsersCanLogout()
    {
        $user = factory(User::class)->create(['email' => 'user@test.com', 'password' => Hash::make('secret')]);
        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/login');
        $this->assertGuest();
    }

    /** @test */
    public function testUsersCanRegister()
    {
        $response = $this->post('/register', [
            'email' => 'user@test.com',
            'password' => 'secret',
        ]);

        $response->assertRedirect('/home');
        $this->assertCount(1, User::all());
        $this->assertEquals('user@test.com', User::first()->email);
        $this->assertAuthenticatedAs(User::first());
    }
}
