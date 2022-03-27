<?php

namespace Tests\Feature\Controllers;

use App\Models\Category;
use App\Models\Color;
use App\Models\Product;
use App\Models\Size;
use App\Models\User;
use App\Services\ImageProfiler;
use App\Services\Interfaces\ImageProfilerInterface;
use App\Services\Interfaces\ProductRepositoryInterface;
use App\Services\Repositories\ProductRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Tests\TestCase;

class ProductControllerTest extends TestCase
{
    use RefreshDatabase;

    protected User $user_admin;

    protected UploadedFile $image;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $localfile = dirname(__DIR__) . '/test_files/image.png';

        $this->image = new UploadedFile(
            $localfile,
            'image.png',
            'image/*',
            null,
            true
        );
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->user_admin = User::factory()->createOne(['status' => User::$status_admin]);
    }

    public function testEditDataWithoutEditImagesModelSaveFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => null]);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->id,
            'color_id' => $product->id,
            'size_id' => $product->id,
            'price' => 125,
            'discount_price' => 110,
            'count' => 1,
        ];

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($model_mock);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.edit'), $data)
            ->assertRedirect(route('admin.product.edit.form', ['id' => $product->id]));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }

    public function testEditDataSuccessWithEditMainImage()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => null]);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->id,
            'color_id' => $product->id,
            'size_id' => $product->id,
            'price' => 125,
            'discount_price' => 110,
            'count' => 1,
            'main_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['main_image'])
            ->willReturn('new_image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with($product->main_image)
            ->willReturn(true);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($product);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $this->assertDatabaseMissing(Product::class, ['main_image' => 'new_image_hash.png']);

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.edit'), $data)
            ->assertRedirect(route('admin.products'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_success');

        $this->assertDatabaseHas(Product::class, ['main_image' => 'new_image_hash.png']);
    }

    public function testEditDataWithMainImageNewImageDeleteFailedIfModelSaveFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => null]);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->id,
            'color_id' => $product->id,
            'size_id' => $product->id,
            'price' => 125,
            'discount_price' => 110,
            'count' => 1,
            'main_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['main_image'])
            ->willReturn('new_image_hash.png');

        $profiler_mock->expects($this->once(0))
            ->method('deleteImage')
            ->with('new_image_hash.png')
            ->willReturn(false);

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($model_mock);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.edit'), $data)
            ->assertRedirect(route('admin.product.edit.form', ['id' => $product->id]));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }

    public function testEditDataWithMainImageOldImageDeleteFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => null]);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->id,
            'color_id' => $product->id,
            'size_id' => $product->id,
            'price' => 125,
            'discount_price' => 110,
            'count' => 1,
            'main_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['main_image'])
            ->willReturn('new_image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with($product['main_image'])
            ->willReturn(false);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($product);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $this->assertDatabaseMissing(Product::class, ['main_image' => 'new_image_hash.png']);

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.edit'), $data)
            ->assertRedirect(route('admin.products'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_warning');

        $this->assertDatabaseHas(Product::class, ['main_image' => 'new_image_hash.png']);
    }

    public function testEditDataWithMainImageNewImageSaveFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => null]);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->id,
            'color_id' => $product->id,
            'size_id' => $product->id,
            'price' => 125,
            'discount_price' => 110,
            'count' => 1,
            'main_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['main_image'])
            ->willReturn(false);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($product);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.edit'), $data)
            ->assertRedirect(route('admin.product.edit.form', ['id' => $product->id]));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');

        $this->assertDatabaseHas(Product::class, ['main_image' => 'image1.png']);
    }

    public function testEditDataSuccessWithEditPreviewImage()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => 'image2.png']);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->id,
            'color_id' => $product->id,
            'size_id' => $product->id,
            'price' => 125,
            'discount_price' => 110,
            'count' => 1,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['preview_image'])
            ->willReturn('new_image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with($product->preview_image)
            ->willReturn(true);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($product);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $this->assertDatabaseMissing(Product::class, ['preview_image' => 'new_image_hash.png']);

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.edit'), $data)
            ->assertRedirect(route('admin.products'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_success');

        $this->assertDatabaseHas(Product::class, ['preview_image' => 'new_image_hash.png']);
    }

    public function testEditDataWithPreviewImageNewImageDeleteFailedIfModelSaveFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => 'image2.png']);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->id,
            'color_id' => $product->id,
            'size_id' => $product->id,
            'price' => 125,
            'discount_price' => 110,
            'count' => 1,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['preview_image'])
            ->willReturn('new_image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with('new_image_hash.png')
            ->willReturn(false);

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($model_mock);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.edit'), $data)
            ->assertRedirect(route('admin.product.edit.form', ['id' => $product->id]));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }

    public function testEditDataWithPreviewImageOldImageDeleteFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => 'image2.png']);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->id,
            'color_id' => $product->id,
            'size_id' => $product->id,
            'price' => 125,
            'discount_price' => 110,
            'count' => 1,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['preview_image'])
            ->willReturn('new_image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with($product['preview_image'])
            ->willReturn(false);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($product);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $this->assertDatabaseMissing(Product::class, ['preview_image' => 'new_image_hash.png']);

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.edit'), $data)
            ->assertRedirect(route('admin.products'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_warning');

        $this->assertDatabaseHas(Product::class, ['preview_image' => 'new_image_hash.png']);
    }

    public function testEditDataWithPreviewImageNewImageSaveFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => 'image2.png']);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->id,
            'color_id' => $product->id,
            'size_id' => $product->id,
            'price' => 125,
            'discount_price' => 110,
            'count' => 1,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['preview_image'])
            ->willReturn(false);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($product);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.edit'), $data)
            ->assertRedirect(route('admin.product.edit.form', ['id' => $product->id]));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');

        $this->assertDatabaseHas(Product::class, ['preview_image' => 'image2.png']);
    }

    public function testEditDataSuccessWithEditTwoImages()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => 'image2.png']);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->id,
            'color_id' => $product->id,
            'size_id' => $product->id,
            'price' => 125,
            'discount_price' => 110,
            'count' => 1,
            'main_image' => $this->image,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->at(0))
            ->method('saveImage')
            ->with($data['main_image'])
            ->willReturn('new_image_hash1.png');

        $profiler_mock->expects($this->at(1))
            ->method('saveImage')
            ->with($data['preview_image'])
            ->willReturn('new_image_hash2.png');

        $profiler_mock->expects($this->at(2))
            ->method('deleteImage')
            ->with($product->main_image)
            ->willReturn(true);

        $profiler_mock->expects($this->at(3))
            ->method('deleteImage')
            ->with($product->preview_image)
            ->willReturn(true);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($product);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $this->assertDatabaseMissing(Product::class, ['main_image' => 'new_image_hash1.png', 'preview_image' => 'new_image_hash2.png']);

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.edit'), $data)
            ->assertRedirect(route('admin.products'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_success');

        $this->assertDatabaseHas(Product::class, ['main_image' => 'new_image_hash1.png', 'preview_image' => 'new_image_hash2.png']);
    }

    public function testEditDataWithTwoImagesNewImagesDeleteFailedIfModelSaveFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => 'image2.png']);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->id,
            'color_id' => $product->id,
            'size_id' => $product->id,
            'price' => 125,
            'discount_price' => 110,
            'count' => 1,
            'main_image' => $this->image,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->at(0))
            ->method('saveImage')
            ->with($data['main_image'])
            ->willReturn('new_image_hash1.png');

        $profiler_mock->expects($this->at(1))
            ->method('saveImage')
            ->with($data['preview_image'])
            ->willReturn('new_image_hash2.png');

        $profiler_mock->expects($this->at(2))
            ->method('deleteImage')
            ->with('new_image_hash1.png')
            ->willReturn(false);

        $profiler_mock->expects($this->at(3))
            ->method('deleteImage')
            ->with('new_image_hash2.png')
            ->willReturn(false);

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($model_mock);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.edit'), $data)
            ->assertRedirect(route('admin.product.edit.form', ['id' => $product->id]));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }

    public function testEditDataWithTwoImagesOldImagesDeleteFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => 'image2.png']);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->id,
            'color_id' => $product->id,
            'size_id' => $product->id,
            'price' => 125,
            'discount_price' => 110,
            'count' => 1,
            'main_image' => $this->image,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->at(0))
            ->method('saveImage')
            ->with($data['main_image'])
            ->willReturn('new_image_hash1.png');

        $profiler_mock->expects($this->at(1))
            ->method('saveImage')
            ->with($data['preview_image'])
            ->willReturn('new_image_hash2.png');

        $profiler_mock->expects($this->at(2))
            ->method('deleteImage')
            ->with($product->main_image)
            ->willReturn(false);

        $profiler_mock->expects($this->at(3))
            ->method('deleteImage')
            ->with($product->preview_image)
            ->willReturn(false);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($product);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $this->assertDatabaseMissing(Product::class, ['main_image' => 'new_image_hash1.png', 'preview_image' => 'new_image_hash2.png']);

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.edit'), $data)
            ->assertRedirect(route('admin.products'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_warning');

        $this->assertDatabaseHas(Product::class, ['main_image' => 'new_image_hash1.png', 'preview_image' => 'new_image_hash2.png']);
    }

    public function testEditDataWithTwoImagesPreviewSaveFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => 'image2.png']);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->id,
            'color_id' => $product->id,
            'size_id' => $product->id,
            'price' => 125,
            'discount_price' => 110,
            'count' => 1,
            'main_image' => $this->image,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->at(0))
            ->method('saveImage')
            ->with($data['main_image'])
            ->willReturn('new_image_hash1.png');

        $profiler_mock->expects($this->at(1))
            ->method('saveImage')
            ->with($data['preview_image'])
            ->willReturn(false);

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with('new_image_hash1.png')
            ->willReturn(false);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($product);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.edit'), $data)
            ->assertRedirect(route('admin.product.edit.form', ['id' => $product->id]));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');

        $this->assertDatabaseHas(Product::class, ['main_image' => 'image1.png', 'preview_image' => 'image2.png']);
    }

    public function testCreateProductSuccessOnlyMainImage()
    {
        $category = Category::factory()->createOne();
        $color = Color::factory()->createOne();
        $size = Size::factory()->createOne();

        $request_data = [
            'name' => 'name',
            'subname' => 'sub',
            'description' => 'descr',
            'category_id' => $category->id,
            'color_id' => $color->id,
            'size_id' => $size->id,
            'price' => '120',
            'discount_price' => null,
            'count' => 2,
            'main_image' => $this->image
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($request_data['main_image'])
            ->willReturn('image_hash.png');

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.create'), $request_data)
            ->assertRedirect(route('admin.products'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_success');

        $request_data['main_image'] = 'image_hash.png';
        $this->assertDatabaseHas(Product::class, $request_data);
    }

    public function testCreateProductSuccessTwoImages()
    {
        $category = Category::factory()->createOne();
        $color = Color::factory()->createOne();
        $size = Size::factory()->createOne();

        $request_data = [
            'name' => 'name',
            'subname' => 'sub',
            'description' => 'descr',
            'category_id' => $category->id,
            'color_id' => $color->id,
            'size_id' => $size->id,
            'price' => '120',
            'discount_price' => null,
            'count' => 2,
            'main_image' => $this->image,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveTwoImages'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveTwoImages')
            ->with($request_data['main_image'], $request_data['preview_image'])
            ->willReturn(['image1_hash.png', 'image2_hash.png']);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.create'), $request_data)
            ->assertRedirect(route('admin.products'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_success');

        $request_data['main_image'] = 'image1_hash.png';
        $request_data['preview_image'] = 'image2_hash.png';
        $this->assertDatabaseHas(Product::class, $request_data);
    }

    public function testCreateProductTwoImagesFailedImageSave()
    {
        $category = Category::factory()->createOne();
        $color = Color::factory()->createOne();
        $size = Size::factory()->createOne();

        $request_data = [
            'name' => 'name',
            'subname' => 'sub',
            'description' => 'descr',
            'category_id' => $category->id,
            'color_id' => $color->id,
            'size_id' => $size->id,
            'price' => '120',
            'discount_price' => null,
            'count' => 2,
            'main_image' => $this->image,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveTwoImages'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveTwoImages')
            ->with($request_data['main_image'], $request_data['preview_image'])
            ->willReturn(false);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.create'), $request_data)
            ->assertRedirect(route('admin.product.create.form'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }

    public function testCreateProductOnlyMainImageFailedImageSave()
    {
        $category = Category::factory()->createOne();
        $color = Color::factory()->createOne();
        $size = Size::factory()->createOne();

        $request_data = [
            'name' => 'name',
            'subname' => 'sub',
            'description' => 'descr',
            'category_id' => $category->id,
            'color_id' => $color->id,
            'size_id' => $size->id,
            'price' => '120',
            'discount_price' => null,
            'count' => 2,
            'main_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($request_data['main_image'])
            ->willReturn(false);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.create'), $request_data)
            ->assertRedirect(route('admin.product.create.form'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }

    public function testCreateProductOnlyMainImageFailedModelSave()
    {
        $category = Category::factory()->createOne();
        $color = Color::factory()->createOne();
        $size = Size::factory()->createOne();

        $request_data = [
            'name' => 'name',
            'subname' => 'sub',
            'description' => 'descr',
            'category_id' => $category->id,
            'color_id' => $color->id,
            'size_id' => $size->id,
            'price' => '120',
            'discount_price' => null,
            'count' => 2,
            'main_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($request_data['main_image'])
            ->willReturn('image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with('image_hash.png');

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $this->instance(
            Product::class,
            $model_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.create'), $request_data)
            ->assertRedirect(route('admin.product.create.form'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');

        $request_data['main_image'] = 'image_hash.png';
        $this->assertDatabaseMissing(Product::class, $request_data);
    }

    public function testCreateProductFailedModelSave()
    {
        $category = Category::factory()->createOne();
        $color = Color::factory()->createOne();
        $size = Size::factory()->createOne();

        $request_data = [
            'name' => 'name',
            'subname' => 'sub',
            'description' => 'descr',
            'category_id' => $category->id,
            'color_id' => $color->id,
            'size_id' => $size->id,
            'price' => '120',
            'discount_price' => null,
            'count' => 2,
            'main_image' => $this->image,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveTwoImages', 'deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveTwoImages')
            ->with($request_data['main_image'], $request_data['preview_image'])
            ->willReturn(['image1_hash.png', 'image2_hash.png']);

        $profiler_mock->expects($this->at(1))
            ->method('deleteImage')
            ->with('image1_hash.png')
            ->willReturn(true);

        $profiler_mock->expects($this->at(2))
            ->method('deleteImage')
            ->with('image2_hash.png')
            ->willReturn(true);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $this->instance(
            Product::class,
            $model_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.create'), $request_data)
            ->assertRedirect(route('admin.product.create.form'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');

        $request_data['main_image'] = 'image1_hash.png';
        $request_data['preview_image'] = 'image2_hash.png';
        $this->assertDatabaseMissing(Product::class, $request_data);
    }

    public function testDeleteProductSuccessWithOneModelImage()
    {
        $product = Product::factory()->createOne(['main_image' => 'image.png']);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($product);

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with($product->main_image)
            ->willReturn(true);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.delete'), ['id' => $product->id])
            ->assertRedirect(route('admin.products'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_success');

        $this->assertDatabaseMissing(Product::class, $product->attributesToArray());
    }

    public function testDeleteProductModelDeleteFailed()
    {
        $product = Product::factory()->createOne();

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(false);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($model_mock);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.delete'), ['id' => $product->id])
            ->assertRedirect(route('admin.products'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');

        $this->assertDatabaseHas(Product::class, $product->attributesToArray());
    }

    public function testDeleteProductWithOneImageImageDeleteFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image.png']);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($product);

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with($product->main_image)
            ->willReturn(false);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.delete'), ['id' => $product->id])
            ->assertRedirect(route('admin.products'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_warning');

        $this->assertDatabaseMissing(Product::class, $product->attributesToArray());
    }

    public function testDeleteProductSuccessWithTwoModelImage()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => 'image2.png']);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($product);

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->at(0))
            ->method('deleteImage')
            ->with($product->main_image)
            ->willReturn(true);

        $profiler_mock->expects($this->at(1))
            ->method('deleteImage')
            ->with($product->preview_image)
            ->willReturn(true);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.delete'), ['id' => $product->id])
            ->assertRedirect(route('admin.products'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_success');

        $this->assertDatabaseMissing(Product::class, $product->attributesToArray());
    }

    public function testDeleteProductWithTwoImagesFirstImageDeleteFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image.png', 'preview_image' => 'image2.png']);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($product);

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->at(0))
            ->method('deleteImage')
            ->with($product->main_image)
            ->willReturn(false);

        $profiler_mock->expects($this->at(1))
            ->method('deleteImage')
            ->with($product->preview_image)
            ->willReturn(true);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.delete'), ['id' => $product->id])
            ->assertRedirect(route('admin.products'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_warning');

        $this->assertDatabaseMissing(Product::class, $product->attributesToArray());
    }

    public function testDeleteProductWithTwoImagesSecondImageDeleteFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image.png', 'preview_image' => 'image2.png']);

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($product);

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteImage'])
            ->getMock();

        $profiler_mock->expects($this->at(0))
            ->method('deleteImage')
            ->with($product->main_image)
            ->willReturn(true);

        $profiler_mock->expects($this->at(1))
            ->method('deleteImage')
            ->with($product->preview_image)
            ->willReturn(false);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.delete'), ['id' => $product->id])
            ->assertRedirect(route('admin.products'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_warning');

        $this->assertDatabaseMissing(Product::class, $product->attributesToArray());
    }

    public function testEditProductDataSuccessWithoutEditImages()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => null]);
        $category_edited_id = Category::factory()->createOne()->id;
        $color_edited_id = Color::factory()->createOne()->id;
        $size_edited_id = Size::factory()->createOne()->id;

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $category_edited_id,
            'color_id' => $color_edited_id,
            'size_id' => $size_edited_id,
            'price' => 125,
            'discount_price' => 110,
            'count' => 1,
        ];


        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($product);

        $this->instance(
            ProductRepositoryInterface::class,
            $rep_mock
        );

        $this->assertDatabaseMissing(Product::class, $data);

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.edit'), $data)
            ->assertRedirect(route('admin.products'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_success');

        $this->assertDatabaseHas(Product::class, $data);
    }
}
