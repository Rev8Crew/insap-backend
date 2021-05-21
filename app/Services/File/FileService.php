<?php


namespace App\Services\File;


use App\Models\File;
use Illuminate\Http\UploadedFile;

class FileService
{
    public function buildFromUploadedFile(UploadedFile $file) {

    }

    public function create(array $input) : File {
        $file = File::create($input);

        return $file;
    }
}
