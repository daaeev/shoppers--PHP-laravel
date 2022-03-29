<?php

namespace App\Services\Interfaces\divided;

use Illuminate\Database\Eloquent\Collection;

interface GetAllInterface
{
    /**
     * Возвращает все существующие элементы модели,
     * с которой работает репозиторий
     *
     * @return Collection
     */
    public function getAll(): Collection;
}
