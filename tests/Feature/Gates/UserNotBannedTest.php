<?php

namespace Tests\Feature\Gates;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class UserNotBannedTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/not-banned-gate-test-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::get($this->route, function () {
            return true;
        })->middleware('can:notBanned');
    }

    public function testIfNotBanned()
    {
        $user = User::factory()->createOne();

        $this->actingAs($user)
            ->get($this->route)
            ->assertOk();
    }

    public function testIfBanned()
    {
        $user = User::factory()->createOne(['status' => User::$status_banned]);

        $this->actingAs($user)
            ->get($this->route)
            ->assertForbidden();
    }

    public function testIfNotAuth()
    {
        $this->get($this->route)
            ->assertForbidden();
    }
}
