<?php

namespace App\Components\UploadedFile;

use App\Models\UploadedFile;
use Illuminate\Support\Facades\Session;

class UploadedFileFactory
{
    public static function createRecordByIdAndName($uuid, $fileName): int
    {
        return UploadedFile::query()->insertOrIgnore([
            'uuid'          => $uuid,
            'session_uuid'  => Session::token(),
            'file_name'     => $fileName
        ]);
    }
}
