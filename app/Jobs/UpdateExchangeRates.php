<?php

namespace App\Jobs;

use App\Models\Exchange;
use App\Services\Interfaces\ExchangeRatesProcess;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateExchangeRates implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @param Exchange $builder
     * @param ExchangeRatesProcess $exchange
     * @return void
     */
    public function handle(Exchange $builder, ExchangeRatesProcess $exchange)
    {
        dispatch(new self())->delay(now()->addMinutes(config('exchange.interval', 60)))
            ->onQueue(config('exchange.queue_name', 'default'));

        $exchange_rates = $exchange->process();

        if (!empty($exchange_rates)) {
            foreach ($exchange_rates as $curr_code => $exc) {
                $builder->updateOrCreate(
                    ['currency_code' => $curr_code],
                    ['exchange' => $exc]
                );
            }
        } else {
            throw new Exception('Exchange API data is empty', 404);
        }
    }
}
