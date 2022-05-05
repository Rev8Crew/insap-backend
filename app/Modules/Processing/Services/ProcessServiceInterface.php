<?php

namespace App\Modules\Processing\Services;

use App\Modules\Processing\Models\Dto\ProcessFileDto;
use App\Modules\Processing\Models\Process;
use App\Modules\Project\Models\Record;

interface ProcessServiceInterface
{
    /**
     * @param Process $process
     * @param Record $record
     * @param array $params
     * @param ProcessFileDto[] $files
     * @return void
     */
    public function executeProcess(Process $process, Record $record, array $params = [], array $files = []);
}
