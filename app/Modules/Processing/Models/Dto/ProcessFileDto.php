<?php

namespace App\Modules\Processing\Models\Dto;

use App\Traits\Makeable;
use Illuminate\Http\UploadedFile;

class ProcessFileDto
{
    use Makeable;

    private UploadedFile $uploadedFile;

    private string $alias;

    public function __construct(UploadedFile $uploadedFile, string $alias)
    {
        $this->uploadedFile = $uploadedFile;
        $this->alias = $alias;
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
    public function getAlias(): string
    {
        return $this->alias;
    }
}
