<?php

namespace Tests\Feature\Jobs;

use App\Jobs\UpdateExchangeRates;
use App\Models\Exchange;
use App\Services\ExchangeRates\PrivatBankExchangeRates;
use Tests\TestCase;

class UpdateExchangeTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->model_mock = $this->getMockBuilder(Exchange::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->getMock();

        $this->builder_mock = $this->getMockBuilder(Exchange::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['save', 'setRawAttributes'])
            ->addMethods(['first'])
            ->getMock();

        $this->exchange_rates_mock = $this->getMockBuilder(PrivatBankExchangeRates::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['process'])
            ->getMock();
    }

    public function testUpdateIfRowInDbExistsSuccess()
    {
        $exchange_data = ['UAH' => 1, 'USD' => 27, 'EUR' => 29];

        $this->model_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $this->model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with($exchange_data);

        $this->builder_mock->expects($this->once())
            ->method('first')
            ->willReturn($this->model_mock);

        $this->exchange_rates_mock->expects($this->once())
            ->method('process')
            ->willReturn($exchange_data);

        $job = app(UpdateExchangeRates::class);

        $this->expectsJobs(UpdateExchangeRates::class);

        $job->handle($this->builder_mock, $this->exchange_rates_mock);
    }

    public function testUpdateIfRowInDbNotExistsSuccess()
    {
        $exchange_data = ['UAH' => 1, 'USD' => 27, 'EUR' => 29];

        $this->builder_mock->expects($this->once())
            ->method('save')
            ->willReturn(true);

        $this->builder_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with($exchange_data);

        $this->builder_mock->expects($this->once())
            ->method('first')
            ->willReturn(null);

        $this->exchange_rates_mock->expects($this->once())
            ->method('process')
            ->willReturn($exchange_data);

        $job = app(UpdateExchangeRates::class);

        $this->expectsJobs(UpdateExchangeRates::class);

        $job->handle($this->builder_mock, $this->exchange_rates_mock);
    }

    public function testUpdateModelSaveFailed()
    {
        $exchange_data = ['UAH' => 1, 'USD' => 27, 'EUR' => 29];

        $this->model_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $this->model_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with($exchange_data);

        $this->builder_mock->expects($this->once())
            ->method('first')
            ->willReturn($this->model_mock);

        $this->exchange_rates_mock->expects($this->once())
            ->method('process')
            ->willReturn($exchange_data);

        $job = app(UpdateExchangeRates::class);

        $this->expectsJobs(UpdateExchangeRates::class);
        $this->expectException(\Exception::class);

        $job->handle($this->builder_mock, $this->exchange_rates_mock);
    }

    public function testUpdateBuilderModelSaveFailed()
    {
        $exchange_data = ['UAH' => 1, 'USD' => 27, 'EUR' => 29];

        $this->builder_mock->expects($this->once())
            ->method('save')
            ->willReturn(false);

        $this->builder_mock->expects($this->once())
            ->method('setRawAttributes')
            ->with($exchange_data);

        $this->builder_mock->expects($this->once())
            ->method('first')
            ->willReturn(null);

        $this->exchange_rates_mock->expects($this->once())
            ->method('process')
            ->willReturn($exchange_data);

        $job = app(UpdateExchangeRates::class);

        $this->expectsJobs(UpdateExchangeRates::class);
        $this->expectException(\Exception::class);

        $job->handle($this->builder_mock, $this->exchange_rates_mock);
    }
}
