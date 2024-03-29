<?php


namespace App\Modules\Project\Services;


use App\Enums\ActiveStatus;
use App\Enums\ImportStatus;
use App\Enums\Process\ProcessField;
use App\Models\User;
use App\Modules\Plugins\Services\PluginService;
use App\Modules\Processing\Models\Dto\ProcessFileDto;
use App\Modules\Processing\Models\Process;
use App\Modules\Project\DTO\RecordCreateDto;
use App\Modules\Project\DTO\RecordFieldDto;
use App\Modules\Project\Models\Record;
use App\Modules\Project\Models\RecordData;
use App\Modules\Project\Models\RecordInfo;
use App\Modules\Project\Requests\RecordImportRequest;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use MongoDB\BSON\ObjectId;
use Webmozart\Assert\Assert;

/**
 * Class RecordService
 * @package App\Modules\Project\Services
 */
class RecordService
{
    private PluginService $pluginService;

    public function __construct(PluginService $pluginService)
    {
        $this->pluginService = $pluginService;
    }

    /**
     * @param array $input
     * @param RecordData $recordData
     * @param User $user
     * @return Record|Model
     */
    public function create(array $input, RecordData $recordData, User $user) {
        $input['user_id'] = $user->id;
        $input['record_data_id'] = $recordData->id;
        $record = Record::create($input);
        return $record;
    }

    public function getRecordById(int $id): Record
    {
        $record = Record::find($id);

        return $record;
    }

    public function update(Record $record, ?string $name, ?string $description, ?Carbon $date, ?Process $process): bool
    {
        if ($name && $record->name !== $name) {
            $record->fill(['name' => $name]);
        }

        if ($description && $record->description !== $description) {
            $record->fill(['description' => $description]);
        }

        if ($date && $record->date !== $date) {
            $record->fill(['date' => $date]);
        }

        if ($process && $record->process_id !== $process->id) {
            Assert::eq(0, (int)$record->import_status, trans('record.can_not_change_process_if_imported'));

            $record->process()->associate($process);
        }

        if ($process === null && $record->process_id) {
            Assert::eq(0, (int)$record->import_status, trans('record.can_not_change_process_if_imported'));

            $record->process()->delete();
        }

        return $record->save();
    }

    public function createFromDto(RecordCreateDto $dto) : Record
    {
        return Record::create($dto->toArray());
    }

    public function delete(Record $record) : bool
    {
        RecordInfo::where('record_id', $record->id)->delete();

        return $record->delete();
    }

    public function deleteRecordFiles(Record $record)
    {
        // MongoFS
        foreach ($record->files ?? [] as $fileId) {
            if (is_array($fileId)) {
                \MongoGrid::delete(new ObjectId($fileId['$oid']));
                continue;
            }

            \MongoGrid::delete(new ObjectId($fileId));
        }
    }

    public function deleteRecordsInfo(Record $record): bool
    {
        $this->deleteRecordFiles($record);

        $record->import_status = ImportStatus::create(ImportStatus::INITIAL);
        $record->import_log = '';
        $record->save();

        $pluginService = $this->pluginService->getPluginService($record->process->plugin);

        return (bool)$pluginService->deleteDataFromRecord($record);
    }

    public function getRecordInfo(Record $record): Collection
    {
        return $this->pluginService->getPluginService($record->process->plugin)->getDataFromRecord($record);
    }

    public function getRecordsByRecordData(RecordData $recordData): Collection
    {
        return Record::whereHas('recordData', static function (Builder $builder) use ($recordData) {
            $builder->where('id', $recordData->id);
            $builder->where('is_active', ActiveStatus::ACTIVE);
        })->where('is_active', ActiveStatus::ACTIVE)->orderByDesc('order')->get();
    }

    public function deleteRecordsFromRecord(Record $record): bool
    {
        return $this->deleteRecordsInfo($record);
    }

    public function makeFieldDtoFromArray(array $data): \Illuminate\Support\Collection
    {
        return collect($data)->map( fn(string $field) => RecordFieldDto::makeFromArray(json_decode($field, true, 512, JSON_THROW_ON_ERROR)));
    }

    public function getFilesFromFieldsCollectionAndRequest(\Illuminate\Support\Collection $fields, RecordImportRequest $request): \Illuminate\Support\Collection
    {
        return $fields->filter( fn(RecordFieldDto $dto) => $dto->getFieldType()->getValue() === ProcessField::FIELD_FILE)
            ->map( fn(RecordFieldDto $dto) => ProcessFileDto::make($request->file('_file_alias_' . $dto->getAlias()), $dto->getAlias()));
    }

    public function isMultiplyImport(Record $record) {

    }
}
