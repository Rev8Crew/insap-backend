<?php

namespace App\Modules\Project\Services;

use App\Models\User;
use App\Modules\Project\Models\RecordData;
use App\Services\File\FileService;
use Illuminate\Http\UploadedFile;

class RecordDataService
{
    private FileService $fileService;
    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }
    /**
     * @param array $input
     * @param UploadedFile|null $file
     * @param User|null $user
     * @return RecordData
     */
    public function create(array $input, ?UploadedFile $file = null, ?User $user = null) : RecordData {

        if ($file) {
            $input['image_id'] = $this->fileService->createFromUploadedImage($file, $user)->id;
        }

        $recordData = RecordData::create($input);
        return $recordData;
    }

    public function delete(RecordData $recordData) {
        $this->deleteImage($recordData);

        $recordData->delete();
    }

    /**
     * @param RecordData $recordData
     * @param string     $name
     *
     * @return RecordData
     */
    public function changeName(RecordData $recordData, string $name): RecordData
    {
        $recordData->update(['name' => $name]);
        return $recordData;
    }

    /**
     * @param RecordData $recordData
     * @param UploadedFile $uploadedFile
     * @param User $user
     * @return bool
     * @throws \Exception
     */
    public function changeImage(RecordData $recordData, UploadedFile $uploadedFile, User $user): bool
    {
        $this->deleteImage($recordData);
        $file = $this->fileService->createFromUploadedFile($uploadedFile, $user);
        return $recordData->update(['image_id' => $file->id]);
    }

    /**
     * @param RecordData $recordData
     * @throws \Exception
     */
    private function deleteImage(RecordData $recordData) {
        // If already exists
        if ($recordData->image_id) {
            $this->fileService->delete($recordData->imageFile);
        }
    }

    public function getRecordDataById(int $id): ?RecordData {
        return RecordData::whereId($id)->first();
    }
}
