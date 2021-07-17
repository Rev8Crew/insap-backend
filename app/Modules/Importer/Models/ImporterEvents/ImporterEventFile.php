<?php


namespace App\Modules\Importer\Models\ImporterEvents;


use Illuminate\Http\UploadedFile;

class ImporterEventFile
{
    private UploadedFile $uploadedFile;

    private string $type;

    public function __construct(UploadedFile $uploadedFile, string $type)
    {
        $this->uploadedFile = $uploadedFile;
        $this->type = $type;
    }

    /**
     * @return UploadedFile
     */
    public function getUploadedFile(): UploadedFile
    {
        return $this->uploadedFile;
    }

    /**
     * @return string
     */
    public function getType(): string
    {
        return $this->type;
    }
}
