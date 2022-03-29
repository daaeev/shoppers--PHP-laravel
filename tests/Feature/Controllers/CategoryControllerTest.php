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

        $this->assertDatabaseMissing(Category::class, $data);

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.category.create'), $data)
            ->assertRedirect(route('admin.categories'));

        $this->assertDatabaseHas(Category::class, $data);

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_success');
    }

    public function testCreateFailedModelSave()
    {
        $model_mock = $this->getMockBuilder(Category::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $this->instance(
            Category::class,
            $model_mock
        );

        $data = ['name' => Str::random()];

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.category.create'), $data)
            ->assertRedirect(route('admin.categories'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }

    public function testDeleteSuccess()
    {
        $cat = Category::factory()->createOne();

        $rep_mock = $this->getMockBuilder(CategoryRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($cat->id)
            ->willReturn($cat);

        $this->instance(
            CategoryRepositoryInterface::class,
            $rep_mock
        );

        $data = ['id' => $cat->id];

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.category.delete'), $data)
            ->assertRedirect(route('admin.categories'));

        $this->assertDatabaseMissing(Category::class, $cat->attributesToArray());

        $response->assertSessionDoesntHaveErrors();
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
