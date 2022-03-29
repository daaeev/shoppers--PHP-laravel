<?php

namespace Tests\Feature\Controllers;

use App\Models\Color;
use App\Models\User;
use App\Services\Interfaces\ColorRepositoryInterface;
use App\Services\Repositories\ColorRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class ColorControllerTest extends TestCase
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

        $this->assertDatabaseMissing(Color::class, $data);

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.color.create'), $data)
            ->assertRedirect(route('admin.colors'));

        $this->assertDatabaseHas(Color::class, $data);

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_success');
    }

    public function testCreateFailedModelSave()
    {
        $model_mock = $this->getMockBuilder(Color::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $this->instance(
            Color::class,
            $model_mock
        );

        $data = ['name' => Str::random()];

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.color.create'), $data)
            ->assertRedirect(route('admin.colors'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }

    public function testDeleteSuccess()
    {
        $col = Color::factory()->createOne();

        $rep_mock = $this->getMockBuilder(ColorRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($col->id)
            ->willReturn($col);

        $this->instance(
            ColorRepositoryInterface::class,
            $rep_mock
        );

        $data = ['id' => $col->id];

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.color.delete'), $data)
            ->assertRedirect(route('admin.colors'));

        $this->assertDatabaseMissing(Color::class, $col->attributesToArray());

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_success');
    }

    public function testDeleteFailedModelDelete()
    {
        $col = Color::factory()->createOne();

        $model_mock = $this->getMockBuilder(Color::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(false);

        $rep_mock = $this->getMockBuilder(ColorRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($col->id)
            ->willReturn($model_mock);

        $this->instance(
            ColorRepositoryInterface::class,
            $rep_mock
        );

        $data = ['id' => $col->id];

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.color.delete'), $data)
            ->assertRedirect(route('admin.colors'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }
}
