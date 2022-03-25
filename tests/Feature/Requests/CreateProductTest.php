<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\CreateProduct;
use App\Models\Category;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class CreateProductTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-set-role-validation-route';
    protected int $cat;
    protected int $col;
    protected int $size;
    protected UploadedFile $image;

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (CreateProduct $validation) {
            return true;
        });

        $this->cat = Category::factory()->createOne()->id;
        $this->col = Color::factory()->createOne()->id;
        $this->size = Size::factory()->createOne()->id;

        $this->image = new UploadedFile(
            dirname(__DIR__) . '/test_files/image.png',
            'image.png',
            'image/*',
            null,
            true
        );
    }

    public function testSuccessData()
    {
        $data = $this->getSuccessData();

        foreach ($data as $req_data) {
            $response = $this->post($this->route, $req_data)
                ->assertOk();

            $response->assertSessionHasNoErrors();
        }
    }

    public function testFailedData()
    {
        $data = $this->getFailedData();

        foreach ($data as $req_data) {
            $response = $this->post($this->route, $req_data)
                ->assertRedirect();

            $response->assertSessionHasErrors();
        }
    }

    protected function getSuccessData()
    {
        return [
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'price' => 120,
                'discount_price' => null,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => $this->image,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'price' => 120,
                'discount_price' => 100,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
        ];
    }

    protected function getFailedData()
    {
        return [
            [
                'name' => null,
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' =>$this->col,
                'size_id' =>$this->size,
                'price' => '120',
                'discount_price' => null,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => null,
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' =>$this->col,
                'size_id' =>$this->size,
                'price' => '120',
                'discount_price' => null,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => null,
                'category_id' => $this->cat,
                'color_id' =>$this->col,
                'size_id' =>$this->size,
                'price' => '120',
                'discount_price' => null,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => null,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'price' => '120',
                'discount_price' => null,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => 123,
                'color_id' => $this->col,
                'size_id' => $this->size,
                'price' => '120',
                'discount_price' => null,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => null,
                'size_id' => $this->size,
                'price' => '120',
                'discount_price' => null,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => 123,
                'size_id' => $this->size,
                'price' => '120',
                'discount_price' => null,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => null,
                'price' => '120',
                'discount_price' => null,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' => $this->col,
                'size_id' => 123,
                'price' => '120',
                'discount_price' => null,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' =>$this->col,
                'size_id' =>$this->size,
                'price' => 'string',
                'discount_price' => null,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' =>$this->col,
                'size_id' =>$this->size,
                'price' => -1,
                'discount_price' => null,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' =>$this->col,
                'size_id' =>$this->size,
                'price' => 120,
                'discount_price' => -1,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' =>$this->col,
                'size_id' =>$this->size,
                'price' => 120,
                'discount_price' => 'string',
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' =>$this->col,
                'size_id' =>$this->size,
                'price' => 120,
                'discount_price' => 121,
                'count' => 2,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' =>$this->col,
                'size_id' =>$this->size,
                'price' => 120,
                'discount_price' => null,
                'count' => -1,
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' =>$this->col,
                'size_id' =>$this->size,
                'price' => 120,
                'discount_price' => null,
                'count' => 'string',
                'main_image' => $this->image,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' =>$this->col,
                'size_id' =>$this->size,
                'price' => 120,
                'discount_price' => null,
                'count' => 1,
                'main_image' => 'not file',
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' =>$this->col,
                'size_id' =>$this->size,
                'price' => 120,
                'discount_price' => null,
                'count' => 1,
                'main_image' => null,
                'preview_image' => null,
            ],
            [
                'name' => 'name',
                'subname' => 'sub',
                'description' => 'descr',
                'category_id' => $this->cat,
                'color_id' =>$this->col,
                'size_id' =>$this->size,
                'price' => 120,
                'discount_price' => null,
                'count' => 1,
                'main_image' => $this->image,
                'preview_image' => 'not file',
            ],
        ];
    }
}
