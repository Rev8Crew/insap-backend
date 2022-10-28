<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\File\FileService;
use Illuminate\Database\Seeder;
use Illuminate\Http\UploadedFile;

class FileSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $fakeImage = UploadedFile::fake()->image('test_image.png', 300, 300);
        $user = User::find(User::TEST_USER_ID);

        $fileService = app(FileService::class);
        $fileService->createFromUploadedFile($fakeImage, $user);
    }
}
