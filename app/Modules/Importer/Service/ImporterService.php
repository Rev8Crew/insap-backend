<?php


namespace App\Modules\Importer\Service;


use App\Models\User;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Importer\Models\Importer\Importer;
use App\Modules\Importer\Models\Importer\ImporterInstall;
use App\Modules\Importer\Models\ImporterEvents\ImporterEvent;
use App\Modules\Importer\Models\ImporterInterpreter\ImporterInterpreter;
use App\Modules\Project\Models\Record;
use App\Modules\Project\Services\RecordService;
use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpZip\Exception\ZipException;
use PhpZip\ZipFile;
use Throwable;

/**
 * Class ImporterService
 * @package App\Modules\Importer\Service
 */
class ImporterService
{
    private ImporterEventService $importerEventService;
    private RecordService $recordService;

    /**
     * ImporterService constructor.
     * @param ImporterEventService $importerEventService
     * @param RecordService $recordService
     */
    public function __construct(ImporterEventService $importerEventService, RecordService $recordService)
    {
        $this->importerEventService = $importerEventService;
        $this->recordService = $recordService;
    }

    /**
     * @param string $name
     * @param string $description
     * @param string $interpreter_class
     * @param Appliance $appliance
     * @param User $user
     * @param UploadedFile $archive
     * @return Importer|null
     * @throws Exception
     */
    public function create(
        string $name,
        string $description,
        string $interpreter_class,
        Appliance $appliance,
        User $user,
        UploadedFile $archive
    ): ?Importer
    {
        $array = compact('name', 'description', 'interpreter_class');

        $array['appliance_id'] = $appliance->id;
        $array['user_id'] = $user->id;

        $importer = Importer::create($array);

        // Install importer from zip archive
        try {
            $this->install($importer, $archive);
        } catch (Throwable $exception) {
            $importer->delete();
            throw new Exception('[Create] Importer service create failed ...', 0, $exception);
        }

        return $importer;
    }

    /**
     * @param Importer $importer
     * @param UploadedFile $archive
     * @return Importer
     * @throws Exception
     */
    public function updateApp(Importer $importer, UploadedFile $archive): Importer
    {
        $this->deleteApp($importer);

        // Install importer from zip archive
        try {
            $this->install($importer, $archive);
        } catch (Throwable $exception) {
            $importer->delete();
            throw new Exception('[Create] Importer service create failed ...', 0, $exception);
        }

        return $importer;
    }

    /**
     * @param Importer $importer
     * @return bool
     */
    protected function deleteApp(Importer $importer): bool
    {
        return Storage::disk('import')->deleteDirectory($importer->id);
    }

    /**
     * @param Importer $importer
     * @param UploadedFile $archive
     * @throws Exception|Throwable
     */
    protected function install(Importer $importer, UploadedFile $archive)
    {
        $storage = Storage::disk('import');
        $storage->makeDirectory($importer->id);

        $path = $importer->getStoragePath();

        try {
            $zip = new ZipFile();
            $zip->openFile($archive->getRealPath())
                ->extractTo($path);
        } catch (ZipException $exception) {
            Log::error("Can't Open/Extract file '{$archive->getRealPath()}' to '$path'", [
                'uploadedFile' => $archive,
                'path' => $path,
                'importer' => $importer
            ]);
            throw new Exception("Can't Open/Extract file '{$archive->getRealPath()}' to '$path'", 0, $exception);
        }

        $interpreter = $importer->interpreter_class;
        /** @var ImporterInterpreter $interpreter */
        $interpreter = new $interpreter;

        /** Install requirements */
        $importerInstall = new ImporterInstall($importer, $interpreter);
        $importerInstall->install();

        /** Execute Test Command */
        $interpreter->addArg('--test=', '1');

        $cd = 'cd ' . $importer->getStoragePath();
        $exitCode = $interpreter->execute("$cd;{$interpreter->getAppCommand()}");
        throw_if($exitCode > 0, new Exception('[Install] Exit code greater then 0, [' . $exitCode . ']'));
    }

    public function delete(Importer $importer)
    {
        $this->deleteApp($importer);
        $importer->delete();
        return $importer;
    }

    /**
     * @param Importer $importer
     * @param Record $record
     * @param array $params - request params
     * @param UploadedFile[] $files - request files
     * @throws Throwable
     */
    public function import(Importer $importer, Record $record, array $params = [], array $files = [])
    {
        // Pre import event
        $event = ImporterEvent::EVENT_PRE;

        try {
            DB::beginTransaction();

            // Event before import
            $this->importerEventService->event($event, ImporterEvent::EVENT_TYPE_PRE, $importer->appliance, $params, $files);

            // Processing data
            $data = $this->exec($importer, $params, $files);

            // Event with processed data
            $this->importerEventService->event($event, ImporterEvent::EVENT_TYPE_POST_BEFORE_DB, $importer->appliance, $params, $files, $data);

            // Add to DB
            $this->addToDatabase($record, $data);

            // Event after DB
            $this->importerEventService->event($event, ImporterEvent::EVENT_TYPE_POST_AFTER_DB, $importer->appliance, $params, $files, $data);

            DB::commit();
        } catch (Exception $exception) {
            // If smth goes wrong
            DB::rollBack();
            $this->recordService->delete($record);
        }

    }

    /**
     * @param Importer $importer
     * @param array $params
     * @param array $files
     * @return array
     */
    protected function exec(Importer $importer, array $params, array $files): array
    {
        return $importer->exec($params, $files);
    }

    /**
     * @param Record $record
     * @param array $data
     */
    protected function addToDatabase(Record $record, array $data)
    {
        $chunk = [];
        foreach ($data as $array) {
            // Add record_id to each record
            $array['record_id'] = $record->id;

            $chunk[] = $array;
            if (count($chunk) == 1000) {
                Record::insert($chunk);
                $chunk = [];
            }

        }
    }


}
