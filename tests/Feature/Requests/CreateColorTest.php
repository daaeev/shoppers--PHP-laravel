<?php

namespace Tests\Feature\Requests;

use App\Http\Requests\CreateColor;
use App\Models\Color;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;
use Tests\TestCase;

class CreateColorTest extends TestCase
{
    use RefreshDatabase;

    protected string $route = '/check-create-color-validation-route';

    public function setUp(): void
    {
        parent::setUp();

        Route::post($this->route, function (CreateColor $validate) {
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
        $col = Color::factory()->createOne();

        $data = [
            null,
            $col->name,
            Str::random(256),
        ];

        foreach ($data as $name_el) {
            $response = $this->post($this->route, [
                'name' => $name_el,
            ])
                ->assertRedirect(route('home'));

            $response->assertSessionHasErrors();
        }
    }
}
