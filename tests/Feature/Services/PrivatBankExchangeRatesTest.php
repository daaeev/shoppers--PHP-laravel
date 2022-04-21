<?php

namespace Tests\Feature\Services;

use App\Services\ExchangeRates\PrivatBankExchangeApiDataGet;
use App\Services\ExchangeRates\PrivatBankExchangeRates;
use App\Services\Interfaces\ExchangeApiDataGetInterface;
use Tests\TestCase;

class PrivatBankExchangeRatesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->exchangeApi_mock = $this->getMockBuilder(PrivatBankExchangeApiDataGet::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['getAPIExchangeData'])
            ->getMock();

        $this->base_cur = config('exchange.base', 'UAH');
    }

    public function testProcessExchangeSuccess()
    {
        $exchangeAPIData = [
            ['ccy' => 'USD', 'base_ccy' => $this->base_cur, 'sale' => 27],
            ['ccy' => 'EUR', 'base_ccy' => $this->base_cur, 'sale' => 29],
        ];

        $this->exchangeApi_mock->expects($this->once())
            ->method('getAPIExchangeData')
            ->willReturn(json_encode($exchangeAPIData));

        $this->instance(ExchangeApiDataGetInterface::class, $this->exchangeApi_mock);

        $object = app(PrivatBankExchangeRates::class);
        $result = $object->process();

        $this->assertArrayHasKey($this->base_cur, $result);
        $this->assertArrayHasKey('USD', $result);
        $this->assertArrayHasKey('EUR', $result);
        $this->assertEquals(1, $result[$this->base_cur]);
        $this->assertEquals(27, $result['USD']);
        $this->assertEquals(29, $result['EUR']);
    }

    public function testProcessExchangeSuccessIfAPIGetNotSupportedCur()
    {
        $exchangeAPIData = [
            ['ccy' => 'USD', 'base_ccy' => $this->base_cur, 'sale' => 27],
            ['ccy' => 'EUR', 'base_ccy' => $this->base_cur, 'sale' => 29],
            ['ccy' => 'ERROR', 'base_ccy' => $this->base_cur, 'sale' => 5],
        ];

        $this->exchangeApi_mock->expects($this->once())
            ->method('getAPIExchangeData')
            ->willReturn(json_encode($exchangeAPIData));

        $this->instance(ExchangeApiDataGetInterface::class, $this->exchangeApi_mock);

        $object = app(PrivatBankExchangeRates::class);
        $result = $object->process();

        $this->assertArrayHasKey($this->base_cur, $result);
        $this->assertArrayHasKey('USD', $result);
        $this->assertArrayHasKey('EUR', $result);
        $this->assertArrayNotHasKey('ERROR', $result);
    }

    public function testProcessExchangeIfApiBaseCurNotEqualToBaseAppCur()
    {
        $exchangeAPIData = [
            ['ccy' => 'USD', 'base_ccy' => $this->base_cur, 'sale' => 27],
            ['ccy' => 'EUR', 'base_ccy' => 'NOT_BASE_CURR', 'sale' => 29],
        ];

        $this->exchangeApi_mock->expects($this->once())
            ->method('getAPIExchangeData')
            ->willReturn(json_encode($exchangeAPIData));

        $this->instance(ExchangeApiDataGetInterface::class, $this->exchangeApi_mock);

        $this->expectException(\Exception::class);

        $object = app(PrivatBankExchangeRates::class);
        $object->process();
    }
}
