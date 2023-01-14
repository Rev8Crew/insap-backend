<?php


namespace App\Services\File;


use App\Enums\ActiveStatus;
use App\Models\File;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use MongoDB\BSON\ObjectId;
use Mts88\MongoGrid\Services\MongoGrid;
use Spatie\LaravelImageOptimizer\Facades\ImageOptimizer;

class FileService
{

    private MongoGrid $mongoGrid;

    public function __construct(MongoGrid $mongoGrid)
    {
        $this->mongoGrid = $mongoGrid;
    }

    public function createFromUploadedFile(UploadedFile $file, ?User $user = null): File
    {
        $storage = Storage::disk('fileStore');
        $name = $file->getClientOriginalName();
        $path = $file->hashName();
        $mime = $file->getMimeType();
        $hashName = $file->hashName();
        $size = $file->getSize();

        $file->move($storage->path(''), $hashName);


        return $this->create([
            'name' => $name,
            'path' => $path,
            'size' => $size,
            'url' => $storage->url($hashName),
            'mime' => $mime,
            'is_active' => ActiveStatus::ACTIVE,
            'user_id' => optional($user)->id
        ]);
    }

    public function createFromUploadedImage(UploadedFile $file, ?User $user = null): File
    {
        ImageOptimizer::optimize($file->getRealPath());
        return $this->createFromUploadedFile($file, $user);
    }

    /**
     * @param array $input
     * @return File
     */
    public function create(array $input): File
    {
        $file = File::create($input);

        return $file;
    }

    public function delete(File $file)
    {
        if (\Storage::disk('fileStore')->exists($file->path) && !\Storage::disk('fileStore')->delete($file->path)) {
            throw new \Exception("Can't delete file: " . $file->path);
        }
        return $file->delete();
    }

    public function getFileInfoFromMongo(string $fileID): array
    {
        $result = [];

        $data = $this->mongoGrid->getFile(new ObjectId($fileID));

        if ($data) {
            $result = [
                'type' => $data['metadata']['type'],
                'filename' => $data['metadata']['filename'],
                'file_uuid' => $data['filename']
            ];
        }

        return $result;
    }
}
