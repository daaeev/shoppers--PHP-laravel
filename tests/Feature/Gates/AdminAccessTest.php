<?php

namespace Gates;

use App\Models\User;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    protected string $route = '/admin-gate-test-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::get($this->route, function () {
            return true;
        })->middleware('can:isAdmin');
    }

    public function testIfAdmin()
    {
        $user = User::factory()->createOne(['status' => User::$status_admin]);

        $this->actingAs($user)
            ->get($this->route)
            ->assertOk();
    }

    public function testIfNotAdmin()
    {
        $user = User::factory()->createOne();

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
