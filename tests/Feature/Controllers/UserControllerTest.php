<?php

namespace Tests\Feature\Controllers;

use App\Models\User;
use App\Services\Interfaces\UserRepositoryInterface;
use App\Services\Repositories\UserRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testSetRoleSuccess()
    {
        $user_admin = User::factory()->createOne(['status' => User::$status_admin]);
        $role_to_set = User::$status_banned;

        $repository_mock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $repository_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($user_admin->id)
            ->willReturn($user_admin);

        $this->instance(
            UserRepositoryInterface::class,
            $repository_mock
        );

        $response = $this->actingAs($user_admin)
            ->post(route('admin.users.role'), [
                'id' => $user_admin->id,
                'role' => $role_to_set,
            ])
            ->assertRedirect(route('admin.users'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_success');

        $user_data = $user_admin->attributesToArray();
        $user_data['status'] = $role_to_set;
        $this->assertDatabaseHas(User::class, $user_data);
    }

    public function testFailedDataSave()
    {
        $user_admin = User::factory()->createOne(['status' => User::$status_admin]);
        $role_to_set = User::$status_banned;

        $user_mock = $this->getMockBuilder(User::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save'])
            ->getMock();

        $user_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $repository_mock = $this->getMockBuilder(UserRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $repository_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($user_admin->id)
            ->willReturn($user_mock);

        $this->instance(
            UserRepositoryInterface::class,
            $repository_mock
        );

        $response = $this->actingAs($user_admin)
            ->post(route('admin.users.role'), [
                'id' => $user_admin->id,
                'role' => $role_to_set,
            ])
            ->assertRedirect(route('admin.users'));

        $response->assertSessionHas('status_failed');

        $user_data = $user_admin->attributesToArray();
        $user_data['status'] = $role_to_set;
        $this->assertDatabaseMissing(User::class, $user_data);
    }
}
