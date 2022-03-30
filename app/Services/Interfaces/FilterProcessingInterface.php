<?php

namespace App\Services\Interfaces;

interface FilterProcessingInterface
{
    /**
     * Проверяет, имеется ли в масиве элементы,
     * ключи которых относятся к группе фильтров,
     * определенные в свойстве $filters.
     *
     * @param array $data
     * @return bool
     */
    public function arrayHasFilters(array $data): bool;

    /**
     * Метод выбирает все элементы массива,
     * ключи которых относятся к группе фильтров,
     * определенные в свойстве $filters.
     *
     * @param array $data
     * @return array
     */
    public function getFiltersFromArray(array $data): array;

    /**
     * Метод обрабатывает массив фильтров:
     *
     * элементы, ключи которых имеют префикс 'filt_',
     * заносятся в подмассив с ключом 'where';
     *
     * элементам с префиксом 'filt_' добавляется постфикс '_id',
     * создавая тем самым имя связующего столбца в таблице;
     *
     * удаляется префикс 'filt_' у всех элементов (имеющие этот префикс);
     *
     * элемент с ключом 'order' заносится в подмассив с ключом 'order',
     * значение элемента разбивается по символу '_' на две строки:
     * ключ первого элемента (первой строки) - 'column', а второго - 'sort'.
     *
     * Результирующий массив должен иметь вид:
     * ['where' => ['category_id' => 1], 'order' => ['column' => 'name', 'sort' => 'asc']]
     *
     * @param array $filters массив фильтров
     * @return array
     */
    public function processFiltersArray(array $filters): array;
}
