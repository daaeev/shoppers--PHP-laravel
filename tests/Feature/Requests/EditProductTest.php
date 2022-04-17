<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\EditProduct;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EditProductTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-edit-product-validation-route';
    protected UploadedFile $image;
    protected int $id;
    protected int $cat;
    protected int $col;
    protected int $size;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);

        $this->image = new UploadedFile(
            dirname(__DIR__) . '/test_files/image.png',
            'image.png',
            'image/*',
            null,
            true
        );
    }

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (EditProduct $validation) {
            return true;
        });

        $product = Product::factory()->createOne();
        $this->id = $product->id;
        $this->col = $product->color_id;
        $this->cat = $product->category_id;
        $this->size = $product->size_id;

        $this->user_admin = User::factory()->createOne(['status' => User::$status_admin]);
    }

    public function testIfNotAuth()
    {
        $response = $this->post($this->route)->assertForbidden();
    }

    public function testIfUserNotAdmin()
    {
        $user = User::factory()->createOne();

        $response = $this->actingAs($user)->post($this->route)->assertForbidden();
    }

    public function testSuccessData()
    {
        $data = $this->getSuccessData();

        foreach ($data as $req_data) {
            $response = $this->actingAs($this->user_admin)->post($this->route, $req_data)
                ->assertOk();

            $response->assertSessionHasNoErrors();
        }
    }

    public function testFailedData()
    {
        $data = $this->getFailedData();

        foreach ($data as $req_data) {
            $response = $this->actingAs($this->user_admin)->post($this->route, $req_data)
                ->assertRedirect();

            $response->assertSessionHasErrors();
        }
    }

    public function getSuccessData()
    {
        return [
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name edited',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub edited',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr edited',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 122,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => 110,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 1,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => $this->image,
            ],
        ];
    }

    public function getFailedData()
    {
        return [
            [
                'id' => 123,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => null,
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => null,
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => null,
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => 123,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => 'string',
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],

            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => 123,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => 'string',
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->cat,
                'currency' => null,
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->cat,
                'currency' => 'maxvalue',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => 123,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => 'string',
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => null,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 'string',
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => -1,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => 'string',
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => -1,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => 121,
                'count' => 2,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 'string',
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => -1,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => 'not file',
                'preview_image' => null,
            ],
            [
                'id' => $this->id,
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'currency' => 'UAH',
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => null,
                'preview_image' => 'not file',
            ],
        ];
    }
}
