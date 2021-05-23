<?php


namespace App\Services\File;


use App\helpers\IsActiveHelper;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class FileService
{
    /**
     * @param UploadedFile $file
     * @param User $user
     * @return File
     */
    public function buildFromUploadedFile(UploadedFile $file, User $user): File
    {
        $storage = Storage::disk('fileStore');
        $file->move( $storage->path(''), $file->hashName());

        return $this->create([
            'name' => $file->getClientOriginalName(),
            'path' => $file->hashName(),
            'url' => $storage->url($file->getRealPath()),
            'mime' => $file->getMimeType(),
            'is_active' => IsActiveHelper::ACTIVE_ACTIVE,
            'user_id' => $user->id
        ]);
    }

    /**
     * @param array $input
     * @return File
     */
    public function create(array $input) : File {
        $file = File::create($input);

        return $file;
    }

    public function delete(File $file) {
        if (!\Storage::disk('fileStore')->delete($file->path)) {
            throw new \Exception("Can't delete file: " . $file->path);
        }
        return $file->delete();
    }
}
