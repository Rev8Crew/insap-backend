<?php

namespace App\Modules\Processing\Models\Dto;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;

class ProcessParamsDto
{
    /** @var array|Collection Request params */
    private Collection $params;

    /** @var ProcessFileDto[]|Collection Request files */
    private Collection $files;

    /** @var array|Collection Processed data */
    private Collection $data;

    /** @var array|Collection Errors */
    private Collection $errors;

    /**
     * ImporterEventParams constructor.
     * @param array $params
     * @param array $data
     */
    public function __construct(array $params, array $data = [])
    {
        $this->data = collect($data);
        $this->params = collect($params);
        $this->files = collect();
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string $type
     */
    public function addFileFromUploaded(UploadedFile $uploadedFile, string $type)
    {
        $this->files->push(new ProcessFileDto($uploadedFile, $type));
    }

    /**
     * @param ProcessFileDto[] $files
     */
    public function setFilesFromUploaded(array $files)
    {
        $this->files = collect($files);
    }

    /**
     * @return array|Collection
     */
    public function getParams(): Collection
    {
        return $this->params;
    }

    /**
     * @return ProcessFileDto[]|Collection
     */
    public function getFiles(): Collection
    {
        return $this->files;
    }

    /**
     * @return array|Collection
     */
    public function getData(): Collection
    {
        return $this->data;
    }

    /**
     * @param array|Collection $errors
     */
    public function setErrors(array $errors): void
    {
        $this->errors = collect($errors);
    }

    /**
     * @param array|Collection $data
     */
    public function setData(array $data): void
    {
        $this->data = collect($data);
    }
}
