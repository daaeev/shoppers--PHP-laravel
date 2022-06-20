<?php

namespace Tests\Feature\Jobs;

use App\Jobs\UpdateExchangeRates;
use App\Models\Exchange;
use App\Services\ExchangeRates\PrivatBankExchangeRates;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class UpdateExchangeTest extends TestCase
{
    protected \PHPUnit\Framework\MockObject\MockObject $builder_mock;
    protected \PHPUnit\Framework\MockObject\MockObject $exchange_rates_mock;

    public function setUp(): void
    {
        parent::setUp();

        $this->builder_mock = $this->getMockBuilder(Exchange::class)
            ->disableOriginalConstructor()
            ->addMethods(['updateOrCreate'])
            ->getMock();

        $this->exchange_rates_mock = $this->getMockBuilder(PrivatBankExchangeRates::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['process'])
            ->getMock();

        Queue::fake([UpdateExchangeRates::class]);
    }

    public function testUpdateSuccess()
    {
        $exchange_data = ['UAH' => 1, 'USD' => 27, 'EUR' => 29];

        $this->builder_mock->expects($this->exactly(count(config('exchange.currencies'))))
            ->method('updateOrCreate')
            ->withConsecutive(
                [['currency_code' => 'UAH'], ['exchange' => 1]],
                [['currency_code' => 'USD'], ['exchange' => 27]],
                [['currency_code' => 'EUR'], ['exchange' => 29]],
            );

        $this->exchange_rates_mock->expects($this->once())
            ->method('process')
            ->willReturn($exchange_data);

        $date = Carbon::createFromDate(2022, 1, 1);
        Carbon::setTestNow($date);

        $job = app(UpdateExchangeRates::class);

        $job->handle($this->builder_mock, $this->exchange_rates_mock);

        Queue::assertPushed(UpdateExchangeRates::class, function ($job) use ($date) {
            if (
                $job->delay == $date->addMinutes(config('exchange.interval', 60))
                && $job->queue == config('exchange.queue_name', 'default')
            ) {
                return true;
            }

            return false;
        });
    }

    public function testUpdateAPIDataGetFailed()
    {
        $this->exchange_rates_mock->expects($this->once())
            ->method('process')
            ->willThrowException(new Exception('', 404));

        $job = app(UpdateExchangeRates::class);

        $this->expectException(\Exception::class);

        $job->handle($this->builder_mock, $this->exchange_rates_mock);
    }
}
