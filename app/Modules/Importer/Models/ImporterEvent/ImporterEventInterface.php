<?php


namespace App\Modules\Importer\Models\ImporterEvent;


use Illuminate\Http\UploadedFile;

interface ImporterEventInterface
{
    public function setParams(array $params);

    /**
     * @param UploadedFile[] $files
     * @return mixed
     */
    public function setFiles(array $files);

    /**
     * @param array $data
     * @return mixed
     */
    public function setProcessedData(array $data);

    public function run();
}
