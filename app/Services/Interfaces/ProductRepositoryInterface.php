<?php

namespace App\Services\Interfaces;

use App\Models\Product;
use App\Services\Interfaces\divided\GetAllForeignInterface;
use App\Services\Interfaces\divided\GetCatalogPaginationInterface;
use App\Services\Interfaces\divided\GetFiltersDataInterface;
use App\Services\Interfaces\divided\GetFirstInterface;
use App\Services\Interfaces\divided\GridInterface;
use Illuminate\Database\Eloquent\Collection;

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
     * @param GetAllForeignInterface ...$repositories
     * @return array
     */
    public function getForeignDataForForm(GetAllForeignInterface ...$repositories): array;

    /**
     * Метод возвращает коллекцию из похожих по размеру товаров,
     * не включая в коллекцию товар $product.
     *
     * @param Product $product
     * @param int $count количество товаров в коллекции
     * @return Collection
     */
    public function getSimilarInSizeProducts(Product $product, int $count = 6): Collection;
}
