<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\CreateSize;
use App\Models\Size;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateSizeTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-create-size-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (CreateSize $validate) {
            return true;
        });
    }

    public function testSuccessData()
    {
        $name = Str::random();

        $response = $this->post($this->route, [
            'name' => $name,
        ])->assertOk();

        $response->assertSessionHasNoErrors();
    }

    public function testFailedData()
    {
        $data = $this->failedData();

        foreach ($data as $name_el) {
            $response = $this->post($this->route, [
                'name' => $name_el,
            ])
                ->assertRedirect(route('home'));

            $response->assertSessionHasErrors();
        }
    }

    protected function failedData()
    {
        $size = Size::factory()->createOne();

        return [
            null,
            $size->name,
            Str::random(256),
        ];
    }
}
