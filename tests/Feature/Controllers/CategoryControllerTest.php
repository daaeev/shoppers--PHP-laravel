<?php

namespace Tests\Feature\Controllers;

use App\Models\Category;
use App\Models\User;
use App\Services\Interfaces\CategoryRepositoryInterface;
use App\Services\Repositories\CategoryRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class CategoryControllerTest extends TestCase
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

        $model_mock = $this->getMockBuilder(Category::class)
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
            Category::class,
            $model_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.category.create'), $data)
            ->assertRedirect(route('admin.categories'));

        $response->assertSessionHas('status_success');
    }

    public function testCreateFailedModelSave()
    {
        $data = ['name' => Str::random()];

        $model_mock = $this->getMockBuilder(Category::class)
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
            Category::class,
            $model_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.category.create'), $data)
            ->assertRedirect(route('admin.categories'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }

    public function testDeleteSuccess()
    {
        $cat = Category::factory()->createOne();

        $model_mock = $this->getMockBuilder(Category::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $rep_mock = $this->getMockBuilder(CategoryRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($cat->id)
            ->willReturn($model_mock);

        $this->instance(
            CategoryRepositoryInterface::class,
            $rep_mock
        );

        $data = ['id' => $cat->id];

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.category.delete'), $data)
            ->assertRedirect(route('admin.categories'));

        $response->assertSessionHas('status_success');
    }

    public function testDeleteFailedModelDelete()
    {
        $cat = Category::factory()->createOne();

        $model_mock = $this->getMockBuilder(Category::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(false);

        $rep_mock = $this->getMockBuilder(CategoryRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($cat->id)
            ->willReturn($model_mock);

        $this->instance(
            CategoryRepositoryInterface::class,
            $rep_mock
        );

        $data = ['id' => $cat->id];

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.category.delete'), $data)
            ->assertRedirect(route('admin.categories'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }
}
