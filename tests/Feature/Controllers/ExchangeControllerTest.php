<?php

namespace Tests\Feature\Controllers;

use App\Jobs\UpdateExchangeRates;
use App\Models\User;
use Illuminate\Database\Query\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Queue;
use Tests\TestCase;

class ExchangeControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->db = $this->getMockBuilder(Builder::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['where', 'exists', 'delete'])
            ->getMock();

        $this->user = User::factory()->createOne(['status' => User::$status_admin]);

        Queue::fake([UpdateExchangeRates::class]);
    }

    public function testUpdateExchangeSuccessIfJobsExists()
    {
        $this->db->expects($this->exactly(2))
            ->method('where')
            ->with('queue', config('exchange.queue_name'))
            ->willReturn($this->db);

        $this->db->expects($this->once())
            ->method('exists')
            ->willReturn(true);

        $this->db->expects($this->once())
            ->method('delete')
            ->willReturn(true);

        DB::shouldReceive('table')
            ->twice()
            ->with('jobs')
            ->andReturn($this->db);

        $date = Carbon::createFromDate(2022, 1, 1);
        Carbon::setTestNow($date);

        $response = $this->actingAs($this->user)
            ->get(route('admin.exchange.update'))
            ->assertRedirect(route('admin.exchange'));

        $response->assertSessionHas('status_success');

        Queue::assertPushed(UpdateExchangeRates::class, function ($job) use ($date) {
            if ($job->queue == config('exchange.queue_name', 'default')) {
                return true;
            }

            return false;
        });
    }

    public function testUpdateExchangeSuccessIfJobsNotExists()
    {
        $this->db->expects($this->once())
            ->method('where')
            ->with('queue', config('exchange.queue_name'))
            ->willReturn($this->db);

        $this->db->expects($this->once())
            ->method('exists')
            ->willReturn(false);

        DB::shouldReceive('table')
            ->once()
            ->with('jobs')
            ->andReturn($this->db);

        $date = Carbon::createFromDate(2022, 1, 1);
        Carbon::setTestNow($date);

        $response = $this->actingAs($this->user)
            ->get(route('admin.exchange.update'))
            ->assertRedirect(route('admin.exchange'));

        $response->assertSessionHas('status_success');

        Queue::assertPushed(UpdateExchangeRates::class, function ($job) use ($date) {
            if ($job->queue == config('exchange.queue_name', 'default')) {
                return true;
            }

            return false;
        });
    }

    public function testUpdateExchangeIfJobsNotExistsAndJobsDeleteFailed()
    {
        $this->db->expects($this->exactly(2))
            ->method('where')
            ->with('queue', config('exchange.queue_name'))
            ->willReturn($this->db);

        $this->db->expects($this->once())
            ->method('exists')
            ->willReturn(true);

        $this->db->expects($this->once())
            ->method('delete')
            ->willReturn(false);

        DB::shouldReceive('table')
            ->twice()
            ->with('jobs')
            ->andReturn($this->db);

        $response = $this->actingAs($this->user)
            ->get(route('admin.exchange.update'))
            ->assertRedirect(route('admin.exchange'));

        $response->assertSessionHas('status_failed');
    }
}
