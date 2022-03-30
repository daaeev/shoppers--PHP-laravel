<?php

namespace App\Services\Interfaces\divided;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface GetCatalogPaginationInterface
{
    /**
     * Метод получает все товары, у которых количество > 0, с использыванием пагинации
     *
     * @param int $pageSize количество элементов на страницу
     * @return mixed
     */
    public function getCatalogWithPag(int $pageSize = 15): LengthAwarePaginator;

    /**
     * Метод получает все товары, у которых количество > 0, с использыванием пагинации.
     * Также учитываются фильтрация данных.
     * Массив $filters должен иметь архитектуру, равной архитектуре
     * результирующего массива метода FilterProcessingInterface::processFiltersArray()
     *
     * @param array $filters массив фильтров
     * @param int $pageSize количество элементов на страницу
     * @return mixed
     */
    public function getCatalogWithPagAndFilters(array $filters, int $pageSize = 15): LengthAwarePaginator;
}
