<?php /** @noinspection SpellCheckingInspection */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property $uuid string
 * @property $session_uuid string
 * @property $file_name string
 */
class UploadedFile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'uuid',
        'session_uuid',
    ];

    public static function getFilePath($fileId, $fileName, $absolutePath = true): string
    {
        return self::getDirPath($fileId, $absolutePath) . "/$fileName";
    }

    public static function getDirPath($fileId, $absolutePath = true): string
    {
        $filePath = "files/$fileId";

        return $absolutePath
            ? storage_path("app/$filePath")
            : $filePath;
    }
}
