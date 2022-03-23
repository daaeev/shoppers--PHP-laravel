<?php

namespace App\Services\Interfaces;

use ViewComponents\Grids\Grid;
use ViewComponents\ViewComponents\Input\InputSource;

interface GridInterface
{
    /**
     * Метод возвращает таблицу из всех элементов,
     * используя библиотеку для построения таблицы (view-components/grid)
     * @param InputSource $input
     * @param int $pageSize количество элементов на странице
     * @return Grid
     */
    public function getAllUsingGrid(InputSource $input, int $pageSize = 15): Grid;
}
