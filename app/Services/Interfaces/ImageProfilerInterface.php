<?php

namespace App\Services\Interfaces;


use Illuminate\Http\UploadedFile;

interface ImageProfilerInterface
{
    /**
     * Сохранение переданного файла-изображения
     *
     * @param UploadedFile $image
     * @return string|false имя изображения или false, при ошибке
     */
    public function saveImage(UploadedFile $image): string|false;

    /**
     * Сохранение двух переданных файлов-изображений.
     * Если при сохранении первого возникает ошибка - второе не сохраняется.
     * Если при сохранении второго возникает ошибка - первое удаляется.
     *
     * @param UploadedFile $first_image
     * @param UploadedFile $second_image
     * @return array|false имена изображений в нумеративном массиве или false, при ошибке
     */
    public function saveTwoImages(UploadedFile $first_image, UploadedFile $second_image): array|false;

    /**
     * Удаление файла-изображения с переданным именем
     *
     * @param string $image_name
     * @return bool
     */
    public function deleteImage(string $image_name): bool;

    /**
     * Метод устанавливает хранилище для сохраняемого изображения в свойство объекта.
     *
     * @param string $disk название хранилища
     * @return self
     */
    public function disk(string $disk): self;

    /**
     * Метод устанавливает директорию относительно директории хранилища,
     * в которую будет сохранено изображение, в свойство объекта.
     *
     * @param string $dir директория относительно директории хранилища
     * @return self
     */
    public function directory(string $dir): self;
}
