<?php

namespace Tests\Feature\Repositories;

use App\Models\Exchange;
use App\Services\Interfaces\ExchangeRepositoryInterface;
use App\Services\Repositories\ExchangeRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Input\InputSource;

class ExchangeRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected ExchangeRepositoryInterface $repository;

    public function setUp(): void
    {
        parent::setUp();

        $this->repository = app(ExchangeRepository::class);
    }

    public function testGetAllUsingGrid()
    {
        $input = new InputSource([]);
        $result = $this->repository->getAllUsingGrid($input);

        $this->assertInstanceOf(Grid::class, $result);
    }

    public function testGetExchangeInfoIfNotExists()
    {
        $this->expectException(ModelNotFoundException::class);

        $this->repository->getExchangeInfo();
    }

    public function testGetExchangeSuccess()
    {
        Exchange::factory()->createOne();

        $data = $this->repository->getExchangeInfo();

        $this->assertArrayNotHasKey('id', $data);

        $currs = config('exchange.currencies');

        foreach ($currs as $cur_code) {
            $this->assertArrayHasKey($cur_code, $data);
        }
    }

    public function testGetExchangeIfCurrencyNotExistsInDB()
    {
        Exchange::factory()->createOne();
        config()->set('exchange.currencies', ['UAH', 'Currency which is not in the database']);

        $this->expectException(\Exception::class);

        $data = $this->repository->getExchangeInfo();
    }
}
