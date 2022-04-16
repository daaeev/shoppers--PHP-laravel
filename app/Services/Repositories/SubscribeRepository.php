<?php

namespace App\Services\Repositories;

use App\Models\Subscribe;

class SubscribeRepository implements \App\Services\Interfaces\SubscribeRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getEmails(): array
    {
        return Subscribe::select('email')->get()->all();
    }
}
