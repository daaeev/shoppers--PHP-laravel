<?php

namespace App\Services\Interfaces\divided;


use Illuminate\Database\Eloquent\Collection;

interface GetFiltersDataInterface
{
    /**
     * Возвращает коллекцию из моделей категорий с количеством
     * существующих товаров с определенной категорией.
     *
     * @return Collection
     */
    public function getCategoriesFilterData(): Collection;

    /**
     * Возвращает коллекцию из моделей цвета с количеством
     * существующих товаров с определенным цветом.
     *
     * @return Collection
     */
    public function getColorsFilterData(): Collection;

    /**
     * Возвращает коллекцию из моделей размера с количеством
     * существующих товаров с определенным размером.
     *
     * @return Collection
     */
    public function getSizesFilterData(): Collection;

    /**
     * Метод возвращает массив коллекций из моделей,
     * с которыми связан продукт (Color, Size...)
     *
     * Метод является неким фасадом, используя
     * существующие методы для получения связных данных
     *
     * Ключи элементов массива создаются по принципу:
     * имя модели, из котороых состоит коллекция, в множественном числе
     * ['Colors' => Collection, ...]
     *
     * @return array
     */
    public function getFiltersData(): array;
}
