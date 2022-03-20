<?php

namespace Tests\Feature\Middlewares;

use App\Models\User;
use App\Services\Interfaces\UserRepositoryInterface;
use App\Services\Repositories\UserRepository;
use Tests\TestCase;
use Illuminate\Support\Facades\Route;

class AdminAccessTest extends TestCase
{
    public function testIfAdmin()
    {
        $user_mock = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isAdmin'])
            ->getMock();

        $user_mock->expects($this->once())
            ->method('isAdmin')
            ->willReturn(true);

        $repository_mock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAuthenticated'])
            ->getMock();

        $repository_mock->expects($this->once())
            ->method('getAuthenticated')
            ->willReturn($user_mock);

        $this->instance(
            UserRepositoryInterface::class,
            $repository_mock
        );

        Route::get('/admin-middleware-test', function () {
            return true;
        })->middleware('admin');

        $this->get('/admin-middleware-test')
            ->assertOk();
    }

    public function testIfNotAdmin()
    {
        $user_mock = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isAdmin'])
            ->getMock();

        $user_mock->expects($this->once())
            ->method('isAdmin')
            ->willReturn(false);

        $repository_mock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAuthenticated'])
            ->getMock();

        $repository_mock->expects($this->once())
            ->method('getAuthenticated')
            ->willReturn($user_mock);

        $this->instance(
            UserRepositoryInterface::class,
            $repository_mock
        );

        Route::get('/admin-middleware-test', function () {
            return true;
        })->middleware('admin');

        $this->get('/admin-middleware-test')
            ->assertRedirect(\route('home'));
    }

    public function testIfNotAuth()
    {
        Route::get('/admin-middleware-test', function () {
            return true;
        })->middleware('admin');

        $this->get('/admin-middleware-test')
            ->assertRedirect(\route('home'));
    }
}
