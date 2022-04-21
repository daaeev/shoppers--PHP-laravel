<?php

namespace Tests\Feature\Services;

use App\Services\ExchangeRates\PrivatBankExchangeApiDataGet;
use App\Services\Wrappers\FileGetContentsWrapper;
use Tests\TestCase;

class PrivatBankExchangeApiDataGetTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->wrapper_mock = $this->getMockBuilder(FileGetContentsWrapper::class)
            ->onlyMethods(['file_get_contents'])
            ->getMock();
    }

    public function testGetDataSuccess()
    {
        $this->wrapper_mock->expects($this->once())
            ->method('file_get_contents')
            ->willReturn(json_encode(['some', 'api', 'data']));

        $this->instance(FileGetContentsWrapper::class, $this->wrapper_mock);

        $object = app(PrivatBankExchangeApiDataGet::class);

        $result = $object->getAPIExchangeData();
        $this->assertNotNull($result);
    }

    public function testGetDataIfDataGetFailed()
    {
        $this->wrapper_mock->expects($this->once())
            ->method('file_get_contents')
            ->willReturn(false);

        $this->instance(FileGetContentsWrapper::class, $this->wrapper_mock);

        $this->expectException(\Exception::class);

        $object = app(PrivatBankExchangeApiDataGet::class);
        $object->getAPIExchangeData();
    }
}
