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
        $repository = new UserRepository;
        $role_to_set = User::$status_banned;
        $user_admin = User::factory()->createOne(['status' => User::$status_admin]);

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
        $this->assertEquals($role_to_set, $repository->getFirstOrNull($user_admin->id)->status);
    }

    /**
     * @dataProvider postFailedDataProvider
     */
    public function testPostDataValidationFailed($id, $role)
    {
        $user_admin = User::factory()->createOne(['status' => User::$status_admin]);

        $response = $this->actingAs($user_admin)
            ->post(route('admin.users.role'), [
                'id' => $id,
                'role' => $role,
            ])
            ->assertRedirect(route('home'));

        $response->assertSessionHasErrors();
    }

    public function postFailedDataProvider()
    {
        $role_to_set = User::$status_banned;

        return [
            ['string', $role_to_set],
            [1, 'string'],
            [123, $role_to_set],
            [1, 123],
            [1.2, $role_to_set],
            [1, 1.2],
            [null, $role_to_set],
            [1, null],
        ];
    }

    public function testFailedDataSave()
    {
        $repository = new UserRepository;
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
        $this->assertNotEquals($role_to_set, $repository->getFirstOrNull($user_admin->id)->status);
    }
}
