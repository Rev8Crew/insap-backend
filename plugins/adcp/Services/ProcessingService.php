<?php

namespace Plugins\adcp\Services;

use App\Enums\Process\ProcessOption;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventParams;
use App\Modules\Plugins\Services\PluginServiceInterface;
use App\Modules\Processing\Models\Dto\ProcessParamsDto;
use App\Modules\Processing\Models\Process;
use App\Modules\Project\Models\Record;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use JsonException;
use Plugins\adcp\Models\Adcp;
use RuntimeException;
use Throwable;

class ProcessingService implements PluginServiceInterface
{
    /**
     * @throws JsonException
     */
    public function validateData(array $data): void
    {
        if (
            isset($data['record_id']) === false ||
            isset($data['max_depth']) === false
        ) {
            throw new RuntimeException('Failed to validate data...' . json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        }
    }

    public function getDataFromRecord(Record $record): Collection
    {
        return Adcp::query()->whereRecordId($record->id)->orderBy('step_id')->get();
    }

    public function addDataToDatabase(Record $record, ProcessParamsDto $paramsDto, Process $process): void
    {
        if ($process->getOptionsByKey(ProcessOption::OVERWRITE_EXISTS_DATA_ON_MULTIPLY_IMPORT) === true) {
            $this->deleteDataFromRecord($record);
        }

        $data = collect($paramsDto->getData());

        $data = $data->map(function ($item) use ($record) {
            $item['depths'] = is_array($item['depths']) ? json_encode($item['depths'], JSON_THROW_ON_ERROR) : $item['depths'];
            $item['record_id'] = $record->id;
            return $item;
        });

        $this->validateData($data->first());

        try {
            DB::beginTransaction();

            foreach ($data->chunk(10) as $chunk) {
                Adcp::insert($chunk->toArray());
            }

            DB::commit();
        } catch (Throwable $throwable) {
            DB::rollBack();
        }
    }

    public function deleteDataFromRecord(Record $record): int
    {
        return Adcp::query()->whereRecordId($record->id)->delete();
    }

    public function isRecordHasImport(Record $record): bool
    {
        return (bool)Adcp::query()->whereRecordId($record->id)->first();
    }
}
