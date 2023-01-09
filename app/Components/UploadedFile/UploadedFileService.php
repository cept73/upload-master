<?php

namespace App\Components\UploadedFile;

use App\Exceptions\ForbiddenException;
use App\Models\UploadedFile;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class UploadedFileService
{
    public static function isOwnByCurrentUser(string $fileUuid): bool
    {
        $uploadedFileWithThisId = UploadedFileRepository::getByUuid($fileUuid);
        if (!$uploadedFileWithThisId) {
            return true;
        }

        return $uploadedFileWithThisId->session_uuid === Session::token();
    }

    /**
     * @throws ForbiddenException
     */
    public static function getFileLength($fileId, $fileName): int
    {
        if (!self::isOwnByCurrentUser($fileId)) {
            throw new ForbiddenException('You have no rights to access this file');
        }

        $realFilePathShort = UploadedFile::getFilePath($fileId, $fileName, false);
        return Storage::exists($realFilePathShort)
            ? Storage::size($realFilePathShort)
            : 0;
    }

    public static function checkFolderExists($fileId): void
    {
        if (!Storage::exists(UploadedFile::getDirPath($fileId))) {
            Storage::makeDirectory(UploadedFile::getDirPath($fileId, false));
        }
    }

    public static function truncateFile($filePath, $newSize): void
    {
        if (file_exists($filePath)) {
            $handle = fopen($filePath, 'r+');
            ftruncate($handle, $newSize);
        } else {
            $handle = fopen($filePath, 'w');
        }

        fclose($handle);
    }
}
