<?php

namespace App\Services\Interfaces\divided;

use Illuminate\Database\Eloquent\Model;

interface GetFirstInterface
{
    /**
     * Метод возвращает экземпляр модели с id = $id,
     * если такой имеется - иначе null
     * @return Model|Null
     */
    public function getFirstOrNull(int $id): Model|null;
}
