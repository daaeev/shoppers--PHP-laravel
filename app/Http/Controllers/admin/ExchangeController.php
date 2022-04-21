<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Jobs\UpdateExchangeRates;
use App\Services\traits\ReturnWithRedirectAndFlash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ExchangeController extends Controller
{
    use ReturnWithRedirectAndFlash;

    /**
     * Метод обновляет курс валют, удаляя старые задания UpdateExchangeRates
     * и создавая новый без отложенной обработки
     *
     * @return mixed
     */
    public function updateExchangeRates()
    {
        if (!$this->deleteJobs()) {
            return $this->withRedirectAndFlash(
                'status_failed',
                'Old jobs delete failed',
                route('admin.exchange'),
                $this->request
            );
        }

        UpdateExchangeRates::dispatch()->onQueue(config('exchange.queue_name', 'default'));

        return $this->withRedirectAndFlash(
            'status_success',
            'Exchange rates update success',
            route('admin.exchange'),
            $this->request
        );
    }

    /**
     * Метод удаляет все задания UpdateExchangeRates
     *
     * @return bool
     */
    protected function deleteJobs(): bool
    {
        // Если в таблице 'Jobs' имеются задания UpdateExchangeRates,
        // и если при их удалении произошла ошибка, то вернуть falsr
        if (DB::table('jobs')->where('queue', config('exchange.queue_name', 'default'))->exists()) {
            if (!DB::table('jobs')->where('queue', config('exchange.queue_name', 'default'))->delete()) {
                return false;
            }
        }

        return true;
    }
}
