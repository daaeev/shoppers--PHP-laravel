<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\EditProduct;
use App\Http\Requests\ProductEditForm;
use App\Models\Product;
use App\Models\User;
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

    /**
     * @dataProvider getFailedData
     */
    public function testFailedData($id)
    {
        $response = $this->actingAs($this->user_admin)->post($this->route, ['id' => $id])
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
