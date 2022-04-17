<?php

namespace App\Services\Repositories;

use App\Models\Subscribe;
use Illuminate\Database\Eloquent\Collection;

class SubscribeRepository implements \App\Services\Interfaces\SubscribeRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getEmails(): Collection
    {
        return Subscribe::select('email')->get();
    }
}
