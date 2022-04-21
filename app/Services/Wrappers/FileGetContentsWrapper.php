<?php

namespace App\Services\Wrappers;

class FileGetContentsWrapper
{
    /**
     * Обёртка над функцией file_get_contents для облегчения тестирования
     *
     * @param string $path
     * @return false|string
     */
    public function file_get_contents(string $path)
    {
        return file_get_contents($path);
    }
}
