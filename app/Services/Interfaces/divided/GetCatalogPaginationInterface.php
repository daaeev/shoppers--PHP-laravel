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
}
