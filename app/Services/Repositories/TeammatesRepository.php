<?php

namespace App\Services\Repositories;

use App\Models\Teammate;
use Illuminate\Database\Eloquent\Collection;

class TeammatesRepository implements \App\Services\Interfaces\TeammatesRepositoryInterface
{
    /**
     * @inheritDoc
     */
    public function getAll(): Collection
    {
        return Teammate::all();
    }
}
