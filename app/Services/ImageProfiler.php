<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImageProfiler implements Interfaces\ImageProfilerInterface
{
    /**
     * @var string идентификатор файлового хранилища
     */
    public $storage_disk = 'public';

    /**
     * @var string директория относительно файлового хранилища для хранения изображений
     */
    public $image_store_dir = 'products_images';

    /**
     * @inheritDoc
     */
    public function saveImage(UploadedFile $image): string|false
    {
        $image_name = Str::random(30) . '.' . $image->getClientOriginalExtension();

        if ($image->storeAs($this->image_store_dir, $image_name, $this->storage_disk)) {
            return $image_name;
        }

        return false;
    }

    /**
     * @inheritDoc
     */
    public function saveTwoImages(UploadedFile $first_image, UploadedFile $second_image): array|false
    {
        $first_image_name = Str::random(30) . '.' . $first_image->getClientOriginalExtension();
        $second_image_name = Str::random(30) . '.' . $second_image->getClientOriginalExtension();

        if (!$first_image->storeAs($this->image_store_dir, $first_image_name, $this->storage_disk)) {
            return false;
        }

        if (!$second_image->storeAs($this->image_store_dir, $second_image_name, $this->storage_disk)) {
            Storage::disk($this->storage_disk)->delete($this->image_store_dir . '/' . $first_image_name);

            return false;
        }

        $images = [$first_image_name, $second_image_name];

        return $images;
    }

    /**
     * @inheritDoc
     */
    public function deleteImage(string $image_name): bool
    {
        if (empty($image_name)) {
            return false;
        }

        if (!Storage::disk($this->storage_disk)->exists($this->image_store_dir . '/' . $image_name)) {
            return true;
        }

        if (Storage::disk($this->storage_disk)->delete($this->image_store_dir . '/' . $image_name)) {
            return true;
        }

        return false;
    }
}
