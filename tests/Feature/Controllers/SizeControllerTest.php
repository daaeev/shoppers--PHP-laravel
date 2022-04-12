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

        $model_mock = $this->getMockBuilder(Size::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with($data);

        $this->instance(
            Size::class,
            $model_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.size.create'), $data)
            ->assertRedirect(route('admin.sizes'));

        $response->assertSessionHas('status_success');
    }

    public function testCreateFailedModelSave()
    {
        $data = ['name' => Str::random()];

        $model_mock = $this->getMockBuilder(Size::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with($data);

        $this->instance(
            Size::class,
            $model_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.size.create'), $data)
            ->assertRedirect(route('admin.sizes'));

        $response->assertSessionHas('status_failed');
    }

    public function testDeleteSuccess()
    {
        $size = Size::factory()->createOne();

        $model_mock = $this->getMockBuilder(Size::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(true);

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

        $response->assertSessionHas('status_failed');
    }
}
