<?php

namespace App\Services\Interfaces\divided;

use Illuminate\Database\Eloquent\Collection;

interface GetRandomInterface
{
    /**
     * Метод возвращает коллекцию из моделей (рандомных)
     * с количеством элементов = $count
     *
     * @param int $count количество элементов
     * @return Collection
     */
    public function getRandom(int $count = 6): Collection;
}
