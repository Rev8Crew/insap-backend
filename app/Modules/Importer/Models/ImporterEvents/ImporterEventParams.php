<?php


namespace App\Modules\Importer\Models\ImporterEvents;


use Illuminate\Http\UploadedFile;

/**
 * Class ImporterEventParams
 * @package App\Modules\Importer\Models\ImporterEvents
 */
class ImporterEventParams
{
    /** @var array Request params */
    private array $params;

    /** @var array Request files */
    private array $files = [];

    /** @var array Processed data */
    private array $data;

    /**
     * ImporterEventParams constructor.
     * @param array $params
     * @param array $data
     */
    public function __construct(array $params, array $data = [])
    {
        $this->params = $params;
        $this->data = $data;
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string $type
     */
    public function addFileFromUploaded(UploadedFile $uploadedFile, string $type)
    {
        $this->files[] = new ImporterEventFile($uploadedFile, $type);
    }

    /**
     * @param ImporterEventFile[] $files
     */
    public function setFilesFromUploaded(array $files)
    {
        $this->files = $files;
    }

    /**
     * @return array
     */
    public function getParams(): array
    {
        return $this->params;
    }

    /**
     * @return array
     */
    public function getFiles(): array
    {
        return $this->files;
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }
}
