<?php

namespace App\Services;

use App\Enums\ZipMethod;
use App\Modules\Processing\Models\Dto\ProcessFileDto;
use Illuminate\Support\Collection;

class ZipperService
{
    public function createAndDownloadZip(string $destinationFile, Collection $files)
    {
        $zipMethod = ZipMethod::create(config('process.zip_method'));

        if ($zipMethod->is(ZipMethod::PHP)) {
            return \Zip::create($destinationFile, $files->map(fn(ProcessFileDto $dto) => $dto->getUploadedFile()->getRealPath()));
        }

        return $destinationFile;
    }
}
