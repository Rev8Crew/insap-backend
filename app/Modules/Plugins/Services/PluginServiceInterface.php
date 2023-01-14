<?php

namespace App\Modules\Plugins\Services;

use App\Modules\Processing\Models\Dto\ProcessParamsDto;
use App\Modules\Processing\Models\Process;
use App\Modules\Project\Models\Record;
use Illuminate\Database\Eloquent\Collection;

interface PluginServiceInterface
{
    public function getDataFromRecord(Record $record): Collection;

    public function isRecordHasImport(Record $record): bool;

    public function addDataToDatabase(Record $record, ProcessParamsDto $paramsDto, Process $process): void;

    public function deleteDataFromRecord(Record $record): int;
}
