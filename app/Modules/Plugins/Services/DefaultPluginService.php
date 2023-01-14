<?php
declare(strict_types=1);

namespace App\Modules\Plugins\Services;

use App\Enums\Process\ProcessOption;
use App\Modules\Processing\Models\Dto\ProcessParamsDto;
use App\Modules\Processing\Models\Process;
use App\Modules\Project\Models\Record;
use App\Modules\Project\Models\RecordInfo;
use Illuminate\Database\Eloquent\Collection;

class DefaultPluginService implements PluginServiceInterface
{
    public function getDataFromRecord(Record $record): Collection
    {
        return RecordInfo::where('record_id', $record->id)->orderBy('step_id')->get();
    }

    public function addDataToDatabase(Record $record, ProcessParamsDto $paramsDto, Process $process): void
    {
        if ($process->getOptionsByKey(ProcessOption::OVERWRITE_EXISTS_DATA_ON_MULTIPLY_IMPORT)) {
            $this->deleteDataFromRecord($record);
        }

        $data = $paramsDto->getData()->all();
        $chunk = [];

        $step = 1;
        foreach ($data as $array) {
            // Add record_id to each record
            $array['record_id'] = $record->id;
            $array['_internal_step_id'] = $step;

            $chunk[] = $array;
            if (count($chunk) === 1000) {

                RecordInfo::insert($chunk);
                $chunk = [];
            }

            $step++;
        }
    }

    public function deleteDataFromRecord(Record $record): int
    {
        return RecordInfo::where('record_id', $record->id)->delete();
    }

    public function isRecordHasImport(Record $record): bool
    {
        return (bool)RecordInfo::where('record_id', $record->id)->orderBy('step_id')->first();
    }
}
