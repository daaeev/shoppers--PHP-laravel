<?php

namespace App\Services\Interfaces;

use App\Services\Interfaces\divided\GetAllForeignInterface;
use App\Services\Interfaces\divided\GetAllInterface;
use App\Services\Interfaces\divided\GetCatalogPaginationInterface;
use App\Services\Interfaces\divided\GetFiltersDataInterface;
use App\Services\Interfaces\divided\GetFirstInterface;
use App\Services\Interfaces\divided\GridInterface;

interface ProductRepositoryInterface extends
    GridInterface,
    GetFirstInterface,
    GetCatalogPaginationInterface,
    GetFiltersDataInterface
{
    /**
     * Метод возвращает массив с коллекциями моделей из таблиц,
     * с которыми связан продукт (colors, sizes, categories...)
     *
     * Результирующий массив имеет количество элементов
     * равное количеству переданных 'репозиториев'
     *
     * Ключами элементов будут имена связанных столбцов,
     * которые беруться с функции интерфейса GetAllForeignInterface:
     * [$repository->getForeignColumnName() => Collection] == ['colors_id' => ColorsCollection, ...]
     *
     * @return array
     */
    public function getForeignDataForForm(GetAllForeignInterface ...$repositories): array;
}
