<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\EditProduct;
use App\Http\Requests\ProductEditForm;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Route;
use Tests\TestCase;

class EditProductFormTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-edit-product-form-validation-route';
    protected int $id;

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (ProductEditForm $validation) {
            return true;
        });

        $this->id = Product::factory()->createOne()->id;
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

    /**
     * @dataProvider getFailedData
     */
    public function testFailedData($id)
    {
        $response = $this->post($this->route, ['id' => $id])
            ->assertRedirect();

        $response->assertSessionHasErrors();
    }

    protected function getSuccessData()
    {
        return [
            ['id' => $this->id],
        ];
    }

    public function getFailedData()
    {
        return [
            [123],
            ['string'],
            [null],
        ];
    }
}
