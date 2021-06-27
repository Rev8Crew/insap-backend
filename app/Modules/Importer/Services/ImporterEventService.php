<?php


namespace App\Modules\Importer\Services;


use App\Modules\Importer\Models\Importer\Importer;
use App\Modules\Importer\Models\ImporterEvents\ImporterEvent;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventDto;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventInstall;
use App\Modules\Importer\Models\ImporterInterpreter\ImporterInterpreter;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpZip\Exception\ZipException;
use PhpZip\ZipFile;
use Throwable;

class ImporterEventService
{
    /**
     * @param ImporterEventDto $eventDto
     * @param UploadedFile $archive
     * @return ImporterEvent
     * @throws Exception
     */
    public function create(ImporterEventDto $eventDto, UploadedFile $archive): ImporterEvent
    {
        $importerEvent = ImporterEvent::create($eventDto->toArray());

        // Install importer from zip archive
        try {
            $this->install($importerEvent, $archive);
        } catch (Throwable $exception) {

            $this->delete($importerEvent);
            throw new Exception('[Create] Importer service create failed ...', 0, $exception);
        }

        return $importerEvent;
    }

    /**
     * @param ImporterEvent $importerEvent
     * @param UploadedFile $archive
     * @throws Throwable
     */
    protected function install(ImporterEvent $importerEvent, UploadedFile $archive)
    {
        $storage = Storage::disk('import');
        $storage->makeDirectory($importerEvent->id);

        $path = $importerEvent->getStoragePath();

        try {
            $zip = new ZipFile();
            $zip->openFile($archive->getRealPath())
                ->extractTo($path);
        } catch (ZipException $exception) {
            Log::error("Can't Open/Extract file '{$archive->getRealPath()}' to '$path'", [
                'uploadedFile' => $archive,
                'path' => $path,
                'importer' => $importerEvent
            ]);
            throw new Exception("Can't Open/Extract file '{$archive->getRealPath()}' to '$path'", 0, $exception);
        }

        $interpreter = $importerEvent->interpreter_class;
        /** @var ImporterInterpreter $interpreter */
        $interpreter = new $interpreter;

        /** Install requirements */
        $importerInstall = new ImporterEventInstall($importerEvent, $interpreter);
        $importerInstall->install();

        /** Execute Test Command */
        $interpreter->addArg('--test=', '1');

        $cd = 'cd ' . $importerEvent->getStoragePath();
        $exitCode = $interpreter->execute("$cd;{$interpreter->getAppCommand()}");
        throw_if($exitCode > 0, new Exception('[Install] Exit code greater then 0, [' . $exitCode . ']'));
    }

    /**
     * @param ImporterEvent $importerEvent
     * @return bool
     */
    protected function deleteApp(ImporterEvent $importerEvent): bool
    {
        return Storage::disk('import')->deleteDirectory($importerEvent->id);
    }

    /**
     * @param ImporterEvent $importerEvent
     */
    public function delete(ImporterEvent $importerEvent)
    {
        $this->deleteApp($importerEvent);
        $importerEvent->delete();
    }

    /**
     * @param ImporterEvent $importerEvent
     * @param UploadedFile $archive
     * @return Importer
     * @throws Exception
     */
    public function updateApp(ImporterEvent $importerEvent, UploadedFile $archive): ImporterEvent
    {
        $this->deleteApp($importerEvent);

        // Install importer from zip archive
        try {
            $this->install($importerEvent, $archive);
        } catch (Throwable $exception) {
            $importerEvent->delete();
            throw new Exception('[Create] Importer service create failed ...', 0, $exception);
        }

        return $importerEvent;
    }
}
