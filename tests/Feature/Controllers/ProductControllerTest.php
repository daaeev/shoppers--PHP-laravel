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
use Illuminate\Support\Facades\Storage;
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
            'size_id' => $size->id, 'currency' => 'UAH',
            'price' => '120',
            'discount_price' => null,
            'count' => 2,
            'main_image' => $this->image
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($request_data['main_image'])
            ->willReturn('image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with([
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $category->id,
                'color_id' => $color->id,
                'size_id' => $size->id, 'currency' => 'UAH',
                'price' => '120',
                'discount_price' => null,
                'count' => 2,
                'main_image' => 'image_hash.png',
                'preview_image' => null
            ]);

        $this->instance(
            Product::class,
            $model_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.create'), $request_data)
            ->assertRedirect(route('admin.products'));

        $response->assertSessionHas('status_success');
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
            'size_id' => $size->id, 'currency' => 'UAH',
            'price' => '120',
            'discount_price' => null,
            'count' => 2,
            'main_image' => $this->image,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveTwoImages', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveTwoImages')
            ->with($request_data['main_image'], $request_data['preview_image'])
            ->willReturn(['image1_hash.png', 'image2_hash.png']);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with([
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $category->id,
                'color_id' => $color->id,
                'size_id' => $size->id, 'currency' => 'UAH',
                'price' => '120',
                'discount_price' => null,
                'count' => 2,
                'main_image' => 'image1_hash.png',
                'preview_image' => 'image2_hash.png',
            ]);

        $this->instance(
            Product::class,
            $model_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.create'), $request_data)
            ->assertRedirect(route('admin.products'));

        $response->assertSessionHas('status_success');
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
            'size_id' => $size->id, 'currency' => 'UAH',
            'price' => '120',
            'discount_price' => null,
            'count' => 2,
            'main_image' => $this->image,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveTwoImages', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveTwoImages')
            ->with($request_data['main_image'], $request_data['preview_image'])
            ->willReturn(false);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.create'), $request_data)
            ->assertRedirect(route('admin.product.create.form'));

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
            'size_id' => $size->id, 'currency' => 'UAH',
            'price' => '120',
            'discount_price' => null,
            'count' => 2,
            'main_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($request_data['main_image'])
            ->willReturn(false);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.create'), $request_data)
            ->assertRedirect(route('admin.product.create.form'));

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
            'size_id' => $size->id, 'currency' => 'UAH',
            'price' => '120',
            'discount_price' => null,
            'count' => 2,
            'main_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($request_data['main_image'])
            ->willReturn('image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with('image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with([
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $category->id,
                'color_id' => $color->id,
                'size_id' => $size->id, 'currency' => 'UAH',
                'price' => '120',
                'discount_price' => null,
                'count' => 2,
                'main_image' => 'image_hash.png',
                'preview_image' => null
            ]);

        $this->instance(
            Product::class,
            $model_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.create'), $request_data)
            ->assertRedirect(route('admin.product.create.form'));

        $response->assertSessionHas('status_failed');
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
            'size_id' => $size->id, 'currency' => 'UAH',
            'price' => '120',
            'discount_price' => null,
            'count' => 2,
            'main_image' => $this->image,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveTwoImages', 'deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveTwoImages')
            ->with($request_data['main_image'], $request_data['preview_image'])
            ->willReturn(['image1_hash.png', 'image2_hash.png']);

        $profiler_mock->expects($this->at(3))
            ->method('deleteImage')
            ->with('image1_hash.png')
            ->willReturn(true);

        $profiler_mock->expects($this->at(4))
            ->method('deleteImage')
            ->with('image2_hash.png')
            ->willReturn(true);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

        $this->instance(
            ImageProfilerInterface::class,
            $profiler_mock
        );

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with([
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $category->id,
                'color_id' => $color->id,
                'size_id' => $size->id, 'currency' => 'UAH',
                'price' => '120',
                'discount_price' => null,
                'count' => 2,
                'main_image' => 'image1_hash.png',
                'preview_image' => 'image2_hash.png',
            ]);

        $this->instance(
            Product::class,
            $model_mock
        );

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.create'), $request_data)
            ->assertRedirect(route('admin.product.create.form'));

        $response->assertSessionHas('status_failed');
    }

    public function testDeleteProductSuccessWithOneModelImage()
    {
        $product = Product::factory()->createOne(['main_image' => 'image.png']);

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $model_mock->id = $product->id;
        $model_mock->main_image = $product->main_image;

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($model_mock);

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with($product->main_image)
            ->willReturn(true);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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

        $response->assertSessionHas('status_success');
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

        $model_mock->id = $product->id;

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

        $response->assertSessionHas('status_failed');
    }

    public function testDeleteProductWithOneImageImageDeleteFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image.png']);

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $model_mock->id = $product->id;
        $model_mock->main_image = $product->main_image;

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($model_mock);

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with($product->main_image)
            ->willReturn(false);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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

        $response->assertSessionHas('status_warning');
    }

    public function testDeleteProductSuccessWithTwoModelImage()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => 'image2.png']);

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $model_mock->id = $product->id;
        $model_mock->main_image = $product->main_image;
        $model_mock->preview_image = $product->preview_image;

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($model_mock);

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->at(2))
            ->method('deleteImage')
            ->with($product->main_image)
            ->willReturn(true);

        $profiler_mock->expects($this->at(3))
            ->method('deleteImage')
            ->with($product->preview_image)
            ->willReturn(true);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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

        $response->assertSessionHas('status_success');
    }

    public function testDeleteProductWithTwoImagesFirstImageDeleteFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image.png', 'preview_image' => 'image2.png']);

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $model_mock->id = $product->id;
        $model_mock->main_image = $product->main_image;
        $model_mock->preview_image = $product->preview_image;

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($model_mock);

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->at(2))
            ->method('deleteImage')
            ->with($product->main_image)
            ->willReturn(false);

        $profiler_mock->expects($this->at(3))
            ->method('deleteImage')
            ->with($product->preview_image)
            ->willReturn(true);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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

        $response->assertSessionHas('status_warning');
    }

    public function testDeleteProductWithTwoImagesSecondImageDeleteFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image.png', 'preview_image' => 'image2.png']);

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['delete'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        $model_mock->id = $product->id;
        $model_mock->main_image = $product->main_image;
        $model_mock->preview_image = $product->preview_image;

        $rep_mock = $this->getMockBuilder(ProductRepository::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getFirstOrNull'])
            ->getMock();

        $rep_mock->expects($this->once())
            ->method('getFirstOrNull')
            ->with($product->id)
            ->willReturn($model_mock);

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->at(2))
            ->method('deleteImage')
            ->with($product->main_image)
            ->willReturn(true);

        $profiler_mock->expects($this->at(3))
            ->method('deleteImage')
            ->with($product->preview_image)
            ->willReturn(false);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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

        $response->assertSessionHas('status_warning');
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
            'price' => 125, 'currency' => 'UAH',
            'discount_price' => 110,
            'count' => 1,
        ];

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with($data);

        $model_mock->id = $product->id;
        $model_mock->main_image = $product->main_image;

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

        $this->assertDatabaseMissing(Product::class, $data);

        $response = $this->actingAs($this->user_admin)
            ->post(route('admin.product.edit'), $data)
            ->assertRedirect(route('admin.products'));

        $response->assertSessionHas('status_success');
    }

    public function testEditDataWithoutEditImagesModelSaveFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => null]);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->category_id,
            'color_id' => $product->color_id,
            'size_id' => $product->size_id,
            'price' => 125, 'currency' => 'UAH',
            'discount_price' => 110,
            'count' => 1,
        ];

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with($data);

        $model_mock->id = $product->id;
        $model_mock->main_image = $product->main_image;

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
            'category_id' => $product->category_id,
            'color_id' => $product->color_id,
            'size_id' => $product->size_id,
            'price' => 125, 'currency' => 'UAH',
            'discount_price' => 110,
            'count' => 1,
            'main_image' => $this->image,
        ];

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with([
                'id' => $product->id,
                'name' => 'edited',
                'subname' => 'edited',
                'description' => 'edited',
                'category_id' => $product->category_id,
                'color_id' => $product->color_id,
                'size_id' => $product->size_id,
                'price' => 125, 'currency' => 'UAH',
                'discount_price' => 110,
                'count' => 1,
                'main_image' => 'new_image_hash.png',
            ]);

        $model_mock->id = $product->id;
        $model_mock->main_image = $product->main_image;

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['main_image'])
            ->willReturn('new_image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with($product->main_image)
            ->willReturn(true);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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
            ->assertRedirect(route('admin.products'));

        $response->assertSessionHas('status_success');
    }

    public function testEditDataWithMainImageNewImageDeleteFailedIfModelSaveFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => null]);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->category_id,
            'color_id' => $product->color_id,
            'size_id' => $product->size_id,
            'price' => 125, 'currency' => 'UAH',
            'discount_price' => 110,
            'count' => 1,
            'main_image' => $this->image,
        ];

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->willReturn([
                'id' => $product->id,
                'name' => 'edited',
                'subname' => 'edited',
                'description' => 'edited',
                'category_id' => $product->category_id,
                'color_id' => $product->color_id,
                'size_id' => $product->size_id,
                'price' => 125, 'currency' => 'UAH',
                'discount_price' => 110,
                'count' => 1,
                'main_image' => 'new_image_hash.png',
            ]);

        $model_mock->id = $product->id;
        $model_mock->main_image = $product->main_image;

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['main_image'])
            ->willReturn('new_image_hash.png');

        $profiler_mock->expects($this->once(0))
            ->method('deleteImage')
            ->with('new_image_hash.png')
            ->willReturn(false);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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
            'category_id' => $product->category_id,
            'color_id' => $product->color_id,
            'size_id' => $product->size_id,
            'price' => 125, 'currency' => 'UAH',
            'discount_price' => 110,
            'count' => 1,
            'main_image' => $this->image,
        ];

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->willReturn([
                'id' => $product->id,
                'name' => 'edited',
                'subname' => 'edited',
                'description' => 'edited',
                'category_id' => $product->category_id,
                'color_id' => $product->color_id,
                'size_id' => $product->size_id,
                'price' => 125, 'currency' => 'UAH',
                'discount_price' => 110,
                'count' => 1,
                'main_image' => 'new_image_hash.png',
            ]);

        $model_mock->id = $product->id;
        $model_mock->main_image = $product->main_image;

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['main_image'])
            ->willReturn('new_image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with($product['main_image'])
            ->willReturn(false);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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
            ->assertRedirect(route('admin.products'));

        $response->assertSessionHas('status_warning');
    }

    public function testEditDataWithMainImageNewImageSaveFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => null]);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->category_id,
            'color_id' => $product->color_id,
            'size_id' => $product->size_id,
            'price' => 125, 'currency' => 'UAH',
            'discount_price' => 110,
            'count' => 1,
            'main_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['main_image'])
            ->willReturn(false);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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

        $response->assertSessionHas('status_failed');
    }

    public function testEditDataSuccessWithEditPreviewImage()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => 'image2.png']);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->category_id,
            'color_id' => $product->color_id,
            'size_id' => $product->size_id,
            'price' => 125, 'currency' => 'UAH',
            'discount_price' => 110,
            'count' => 1,
            'preview_image' => $this->image,
        ];

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->willReturn([
                'id' => $product->id,
                'name' => 'edited',
                'subname' => 'edited',
                'description' => 'edited',
                'category_id' => $product->category_id,
                'color_id' => $product->color_id,
                'size_id' => $product->size_id,
                'price' => 125, 'currency' => 'UAH',
                'discount_price' => 110,
                'count' => 1,
                'preview_image' => 'new_image_hash.png',
            ]);

        $model_mock->id = $product->id;
        $model_mock->main_image = $product->main_image;
        $model_mock->preview_image = $product->preview_image;

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->category_id,
            'color_id' => $product->color_id,
            'size_id' => $product->size_id,
            'price' => 125, 'currency' => 'UAH',
            'discount_price' => 110,
            'count' => 1,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['preview_image'])
            ->willReturn('new_image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with($product->preview_image)
            ->willReturn(true);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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
            ->assertRedirect(route('admin.products'));

        $response->assertSessionHas('status_success');
    }

    public function testEditDataWithPreviewImageNewImageDeleteFailedIfModelSaveFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => 'image2.png']);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->category_id,
            'color_id' => $product->color_id,
            'size_id' => $product->size_id,
            'price' => 125, 'currency' => 'UAH',
            'discount_price' => 110,
            'count' => 1,
            'preview_image' => $this->image,
        ];

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->willReturn([
                'id' => $product->id,
                'name' => 'edited',
                'subname' => 'edited',
                'description' => 'edited',
                'category_id' => $product->category_id,
                'color_id' => $product->color_id,
                'size_id' => $product->size_id,
                'price' => 125, 'currency' => 'UAH',
                'discount_price' => 110,
                'count' => 1,
                'preview_image' => 'new_image_hash.png',
            ]);

        $model_mock->id = $product->id;
        $model_mock->main_image = $product->main_image;
        $model_mock->preview_image = $product->preview_image;

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['preview_image'])
            ->willReturn('new_image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with('new_image_hash.png')
            ->willReturn(false);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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
            'category_id' => $product->category_id,
            'color_id' => $product->color_id,
            'size_id' => $product->size_id,
            'price' => 125, 'currency' => 'UAH',
            'discount_price' => 110,
            'count' => 1,
            'preview_image' => $this->image,
        ];

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->willReturn([
                'id' => $product->id,
                'name' => 'edited',
                'subname' => 'edited',
                'description' => 'edited',
                'category_id' => $product->category_id,
                'color_id' => $product->color_id,
                'size_id' => $product->size_id,
                'price' => 125, 'currency' => 'UAH',
                'discount_price' => 110,
                'count' => 1,
                'preview_image' => 'new_image_hash.png',
            ]);

        $model_mock->id = $product->id;
        $model_mock->main_image = $product->main_image;
        $model_mock->preview_image = $product->preview_image;

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['preview_image'])
            ->willReturn('new_image_hash.png');

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with($product['preview_image'])
            ->willReturn(false);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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
            ->assertRedirect(route('admin.products'));

        $response->assertSessionHas('status_warning');
    }

    public function testEditDataWithPreviewImageNewImageSaveFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => 'image2.png']);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->category_id,
            'color_id' => $product->color_id,
            'size_id' => $product->size_id,
            'price' => 125, 'currency' => 'UAH',
            'discount_price' => 110,
            'count' => 1,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->once())
            ->method('saveImage')
            ->with($data['preview_image'])
            ->willReturn(false);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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

        $response->assertSessionHas('status_failed');
    }

    public function testEditDataSuccessWithEditTwoImages()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => 'image2.png']);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->category_id,
            'color_id' => $product->color_id,
            'size_id' => $product->size_id,
            'price' => 125, 'currency' => 'UAH',
            'discount_price' => 110,
            'count' => 1,
            'main_image' => $this->image,
            'preview_image' => $this->image,
        ];

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->willReturn([
                'id' => $product->id,
                'name' => 'edited',
                'subname' => 'edited',
                'description' => 'edited',
                'category_id' => $product->category_id,
                'color_id' => $product->color_id,
                'size_id' => $product->size_id,
                'price' => 125, 'currency' => 'UAH',
                'discount_price' => 110,
                'count' => 1,
                'main_image' => 'new_image_hash1.png',
                'preview_image' => 'new_image_hash2.png',
            ]);

        $model_mock->id = $product->id;
        $model_mock->main_image = $product->main_image;
        $model_mock->preview_image = $product->preview_image;

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->at(2))
            ->method('saveImage')
            ->with($data['main_image'])
            ->willReturn('new_image_hash1.png');

        $profiler_mock->expects($this->at(3))
            ->method('saveImage')
            ->with($data['preview_image'])
            ->willReturn('new_image_hash2.png');

        $profiler_mock->expects($this->at(4))
            ->method('deleteImage')
            ->with($product->main_image)
            ->willReturn(true);

        $profiler_mock->expects($this->at(5))
            ->method('deleteImage')
            ->with($product->preview_image)
            ->willReturn(true);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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
            ->assertRedirect(route('admin.products'));

        $response->assertSessionHas('status_success');
    }

    public function testEditDataWithTwoImagesNewImagesDeleteFailedIfModelSaveFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => 'image2.png']);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->category_id,
            'color_id' => $product->color_id,
            'size_id' => $product->size_id,
            'price' => 125, 'currency' => 'UAH',
            'discount_price' => 110,
            'count' => 1,
            'main_image' => $this->image,
            'preview_image' => $this->image,
        ];

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->willReturn([
                'id' => $product->id,
                'name' => 'edited',
                'subname' => 'edited',
                'description' => 'edited',
                'category_id' => $product->category_id,
                'color_id' => $product->color_id,
                'size_id' => $product->size_id,
                'price' => 125, 'currency' => 'UAH',
                'discount_price' => 110,
                'count' => 1,
                'main_image' => 'new_image_hash1.png',
                'preview_image' => 'new_image_hash2.png',
            ]);

        $model_mock->id = $product->id;
        $model_mock->main_image = $product->main_image;
        $model_mock->preview_image = $product->preview_image;

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->at(2))
            ->method('saveImage')
            ->with($data['main_image'])
            ->willReturn('new_image_hash1.png');

        $profiler_mock->expects($this->at(3))
            ->method('saveImage')
            ->with($data['preview_image'])
            ->willReturn('new_image_hash2.png');

        $profiler_mock->expects($this->at(4))
            ->method('deleteImage')
            ->with('new_image_hash1.png')
            ->willReturn(false);

        $profiler_mock->expects($this->at(5))
            ->method('deleteImage')
            ->with('new_image_hash2.png')
            ->willReturn(false);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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
            'category_id' => $product->category_id,
            'color_id' => $product->color_id,
            'size_id' => $product->size_id,
            'price' => 125, 'currency' => 'UAH',
            'discount_price' => 110,
            'count' => 1,
            'main_image' => $this->image,
            'preview_image' => $this->image,
        ];

        $model_mock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->willReturn([
                'id' => $product->id,
                'name' => 'edited',
                'subname' => 'edited',
                'description' => 'edited',
                'category_id' => $product->category_id,
                'color_id' => $product->color_id,
                'size_id' => $product->size_id,
                'price' => 125, 'currency' => 'UAH',
                'discount_price' => 110,
                'count' => 1,
                'main_image' => 'new_image_hash1.png',
                'preview_image' => 'new_image_hash2.png',
            ]);

        $model_mock->id = $product->id;
        $model_mock->main_image = $product->main_image;
        $model_mock->preview_image = $product->preview_image;

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->at(2))
            ->method('saveImage')
            ->with($data['main_image'])
            ->willReturn('new_image_hash1.png');

        $profiler_mock->expects($this->at(3))
            ->method('saveImage')
            ->with($data['preview_image'])
            ->willReturn('new_image_hash2.png');

        $profiler_mock->expects($this->at(4))
            ->method('deleteImage')
            ->with($product->main_image)
            ->willReturn(false);

        $profiler_mock->expects($this->at(5))
            ->method('deleteImage')
            ->with($product->preview_image)
            ->willReturn(false);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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
            ->assertRedirect(route('admin.products'));

        $response->assertSessionHas('status_warning');
    }

    public function testEditDataWithTwoImagesPreviewSaveFailed()
    {
        $product = Product::factory()->createOne(['main_image' => 'image1.png', 'preview_image' => 'image2.png']);

        $data = [
            'id' => $product->id,
            'name' => 'edited',
            'subname' => 'edited',
            'description' => 'edited',
            'category_id' => $product->category_id,
            'color_id' => $product->color_id,
            'size_id' => $product->size_id,
            'price' => 125, 'currency' => 'UAH',
            'discount_price' => 110,
            'count' => 1,
            'main_image' => $this->image,
            'preview_image' => $this->image,
        ];

        $profiler_mock = $this->getMockBuilder(ImageProfiler::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['saveImage', 'deleteImage', 'disk', 'directory'])
            ->getMock();

        $profiler_mock->expects($this->at(2))
            ->method('saveImage')
            ->with($data['main_image'])
            ->willReturn('new_image_hash1.png');

        $profiler_mock->expects($this->at(3))
            ->method('saveImage')
            ->with($data['preview_image'])
            ->willReturn(false);

        $profiler_mock->expects($this->once())
            ->method('deleteImage')
            ->with('new_image_hash1.png')
            ->willReturn(false);

        $profiler_mock->expects($this->once())
            ->method('disk')
            ->willReturn($profiler_mock);

        $profiler_mock->expects($this->once())
            ->method('directory')
            ->willReturn($profiler_mock);

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

        $response->assertSessionHas('status_failed');
    }
}
