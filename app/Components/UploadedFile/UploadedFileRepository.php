<?php

namespace App\Components\UploadedFile;

use App\Models\UploadedFile;

class UploadedFileRepository
{
    public static function getByUuid($uuid): ?UploadedFile
    {
        /** @var ?UploadedFile $uploadedFile */
        $uploadedFile = UploadedFile::query()->where(['uuid' => $uuid])->first();
        return $uploadedFile;
    }
}
