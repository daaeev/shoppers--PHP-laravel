<?php

namespace App\Services\Interfaces;

use App\Services\Interfaces\divided\GetAllForeignInterface;
use App\Services\Interfaces\divided\GetFirstInterface;
use App\Services\Interfaces\divided\GridInterface;

interface ProductRepositoryInterface extends GridInterface, GetFirstInterface
{
    /**
     * Метод возвращает массив с коллекциями из таблиц,
     * с которыми связан продукт (colors, sizes, categories...)
     *
     * Результирующий массив должен иметь количество элементов
     * равное количеству таблиц, с которыми связан продукт.
     *
     * Ключами элементов должны быть названия таблиц:
     * ['colors' => ColorsCollection, ...]
     *
     * @return array
     */
    public function getForeignDataForForm(GetAllForeignInterface ...$repositories): array;
}
