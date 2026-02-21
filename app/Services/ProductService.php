<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;

class ProductService
{
    private const PHOTO_DISK = 'public';
    private const PHOTO_DIRECTORY = 'products';

    /**
     * Store product photo and return the stored path.
     *
     * @throws \RuntimeException
     */
    public function storePhoto(UploadedFile $file): string
    {
        return $file->store(self::PHOTO_DIRECTORY, self::PHOTO_DISK);
    }
}
