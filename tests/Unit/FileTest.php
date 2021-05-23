<?php

namespace Tests\Unit;

use App\helpers\IsActiveHelper;
use App\Models\File;
use App\Models\User;
use App\Services\File\FileService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FileTest extends TestCase
{
    private ?User $user;
    private ?FileService $fileService;
    private ?File $file;

    public function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub

        $this->user = User::find(User::TEST_USER_ID);
        $this->fileService = $this->app->make(FileService::class);
        $this->file = File::first();
    }

    public function testCreateFromUploadedFile()
    {
        $uploadedFile = UploadedFile::fake()->image('test.png', 100, 100);

        $this->file = $this->fileService->buildFromUploadedFile($uploadedFile, $this->user);

        $this->assertEquals( $uploadedFile->getClientOriginalName(), $this->file->name);
        $this->assertEquals( $uploadedFile->getMimeType(), $this->file->mime);
        $this->assertEquals( IsActiveHelper::ACTIVE_ACTIVE, $this->file->is_active);
        $this->assertEquals( $this->user->id, $this->file->user->id);

        $this->assertNotNull($this->file->user);
        Storage::disk('fileStore')->assertExists($uploadedFile->hashName())->delete($uploadedFile->hashName());
    }

    public function testDeleteFile() {
        $path = $this->file->path;
        $id = $this->file->id;
        $this->fileService->delete($this->file);

        Storage::disk('fileStore')->assertMissing($path);
        $this->assertNull(File::find($id));
    }
}
