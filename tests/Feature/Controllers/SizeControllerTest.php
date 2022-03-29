<?php

namespace Tests\Feature\Controllers;

use App\Models\Size;
use App\Models\User;
use App\Services\Interfaces\SizeRepositoryInterface;
use App\Services\Repositories\SizeRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class SizeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user_admin = User::factory()->createOne(['status' => User::$status_admin]);
    }

    public function testSuccessCreate()
    {
        $data = ['name' => Str::random()];

        $this->assertDatabaseMissing(Size::class, $data);

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.size.create'), $data)
            ->assertRedirect(route('admin.sizes'));

        $this->assertDatabaseHas(Size::class, $data);

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_success');
    }

    public function testCreateFailedModelSave()
    {
        $model_mock = $this->getMockBuilder(Size::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $this->instance(
            Size::class,
            $model_mock
        );

        $data = ['name' => Str::random()];

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.size.create'), $data)
            ->assertRedirect(route('admin.sizes'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }

    public function testDeleteSuccess()
    {
        $size = Size::factory()->createOne();

        $rep_mock = $this->getMockBuilder(SizeRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($size->id)
            ->willReturn($size);

        $this->instance(
            SizeRepositoryInterface::class,
            $rep_mock
        );

        $data = ['id' => $size->id];

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.size.delete'), $data)
            ->assertRedirect(route('admin.sizes'));

        $this->assertDatabaseMissing(Size::class, $size->attributesToArray());

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_success');
    }

    public function testDeleteFailedModelDelete()
    {
        $size = Size::factory()->createOne();

        $model_mock = $this->getMockBuilder(Size::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(false);

        $rep_mock = $this->getMockBuilder(SizeRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($size->id)
            ->willReturn($model_mock);

        $this->instance(
            SizeRepositoryInterface::class,
            $rep_mock
        );

        $data = ['id' => $size->id];

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.size.delete'), $data)
            ->assertRedirect(route('admin.sizes'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }
}
