<?php

namespace App\Http\Controllers;

use App\Components\Request\RequestRepository;
use App\Components\UploadedFile\UploadedFileFactory;
use App\Components\UploadedFile\UploadedFileService;
use App\Exceptions\ForbiddenException;
use App\Models\UploadedFile;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UploadController extends BaseController
{
    /**
     * @return array
     */
    public function status(): array
    {
        try {
            $fileId     = RequestRepository::get('X-File-Id');
            $fileName   = RequestRepository::get('X-File-Name', true);

            UploadedFileService::isOwnByCurrentUserOrFail($fileId);

            return [
                'status'        => 200,
                'bytes'         => UploadedFileService::getFileLength($fileId, $fileName)
            ];
        }
        catch (Throwable $exception) {
            return [
                'status'        => $exception->getCode(),
                'statusText'    => $exception->getMessage(),
                'bytes'         => 0
            ];
        }
    }

    /**
     * @return array
     * @throws ForbiddenException
     */
    public function upload(): array
    {
        $fileId     = RequestRepository::get('X-File-Id');
        UploadedFileService::isOwnByCurrentUserOrFail($fileId);

        $startAt    = (int) RequestRepository::get('X-Start-Byte');
        $fileName   = RequestRepository::get('X-File-Name', true);
        $filePath   = UploadedFile::getFilePath($fileId, $fileName);
        $filePathShort = UploadedFile::getFilePath($fileId, $fileName, false);
        $realFileNameSize = UploadedFileService::getFileLength($fileId, $fileName);

        if (file_exists($filePath) && $realFileNameSize < $startAt) {
            return [
                'status'        => 400,
                'statusText'    => 'Too early'
            ];
        }

        UploadedFileService::checkFolderExists($fileId);
        if ($realFileNameSize !== $startAt) {
            UploadedFileService::truncateFile($filePath, $startAt);
        }

        $content = request()->getContent();
        if ($startAt > 0) {
            Storage::append($filePathShort, $content);
        } else {
            if ($startAt === 0) {
                UploadedFileFactory::createRecordByIdAndName($fileId, $fileName);
            }

            Storage::put($filePathShort, $content);
        }

        return [
            'status'        => 200,
            'startAt'       => $startAt,
            'nuf'           => $newUploadFile ?? null
        ];
    }
}
