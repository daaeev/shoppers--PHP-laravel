<?php

namespace App\Services\Interfaces\divided;

use Illuminate\Database\Eloquent\Collection;

interface GetAllForeignInterface
{
    /**
     * Возвращает все существующие элементы модели,
     * с которой работает репозиторий
     *
     * @return Collection
     */
    public function getAll(): Collection;

    /**
     * Возвращает имя столбца из таблицы продуктов,
     * который связан с таблицей модели,
     * с которой работает репозиторий (модель репозитория)
     *
     * По правилам, имя столбца создаётся, используя
     * имя модели в нижнем регистре с постфиксом '_id'
     *
     * Репозиторий SizeRepository -> модель Size -> столбец size_id
     *
     * @return string
     */
    public function getForeignColumnName(): string;
}
