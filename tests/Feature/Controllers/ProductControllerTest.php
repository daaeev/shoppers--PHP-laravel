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

    public function setUp(): void
    {
        parent::setUp();

        $this->user_admin = User::factory()->createOne(['status' => User::$status_admin]);
    }

    public function testCreateProductSuccessOnlyMainImage()
    {
        $category = Category::factory()->createOne();
        $color = Color::factory()->createOne();
        $size = Size::factory()->createOne();

        $localfile = dirname(__DIR__) . '/test_files/image.png';
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
            'main_image' => new UploadedFile(
                $localfile,
                'image.png',
                'image/*',
                null,
                true
            ),
            'preview_image' => null,
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

        $localfile = dirname(__DIR__) . '/test_files/image.png';
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
            'main_image' => new UploadedFile(
                $localfile,
                'image.png',
                'image/*',
                null,
                true
            ),
            'preview_image' => new UploadedFile(
                $localfile,
                'image.png',
                'image/*',
                null,
                true
            ),
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

        $localfile = dirname(__DIR__) . '/test_files/image.png';
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
            'main_image' => new UploadedFile(
                $localfile,
                'image.png',
                'image/*',
                null,
                true
            ),
            'preview_image' => new UploadedFile(
                $localfile,
                'image.png',
                'image/*',
                null,
                true
            ),
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
            ->assertRedirect(route('admin.products.create.form'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }

    public function testCreateProductOnlyMainImageFailedImageSave()
    {
        $category = Category::factory()->createOne();
        $color = Color::factory()->createOne();
        $size = Size::factory()->createOne();

        $localfile = dirname(__DIR__) . '/test_files/image.png';
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
            'main_image' => new UploadedFile(
                $localfile,
                'image.png',
                'image/*',
                null,
                true
            ),
            'preview_image' => null,
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
            ->assertRedirect(route('admin.products.create.form'));

        $response->assertSessionDoesntHaveErrors();
        $response->assertSessionHas('status_failed');
    }

    public function testCreateProductOnlyMainImageFailedModelSave()
    {
        $category = Category::factory()->createOne();
        $color = Color::factory()->createOne();
        $size = Size::factory()->createOne();

        $localfile = dirname(__DIR__) . '/test_files/image.png';
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
            'main_image' => new UploadedFile(
                $localfile,
                'image.png',
                'image/*',
                null,
                true
            ),
            'preview_image' => null,
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
            ->assertRedirect(route('admin.products.create.form'));

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

        $localfile = dirname(__DIR__) . '/test_files/image.png';
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
            'main_image' => new UploadedFile(
                $localfile,
                'image.png',
                'image/*',
                null,
                true
            ),
            'preview_image' => new UploadedFile(
                $localfile,
                'image.png',
                'image/*',
                null,
                true
            ),
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
            ->assertRedirect(route('admin.products.create.form'));

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
}
