<?php

namespace App\Jobs;

use App\Models\Exchange;
use App\Services\Interfaces\ExchangeRatesProcess;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
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
            ->onQueue(config('exchange.queue_name'));

        $data = $exchange->process();

        $model = $builder->first();

        // Если запись найдена - изменить её,
        // иначе создать новую
        if ($model) {
            $this->saveModel($model, $data);
        } else {
            $this->saveModel($builder, $data);
        }
    }

    /**
     * Сохранение модели $model со свойствами $data
     *
     * @param Exchange $model экземпляр модели
     * @param array $data свойства, устанавливаемые модели
     * @return void
     * @throws \Exception при возникновении ошибки сохранения данных
     */
    protected function saveModel(Exchange $model, array $data)
    {
        $model->setRawAttributes($data);

        if (!$model->save()) {
            throw new \Exception('Database error');
        }
    }
}
