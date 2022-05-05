<?php

namespace App\Events;

use App\Modules\Processing\Models\Dto\ProcessFileDto;
use App\Modules\Processing\Models\Process;
use App\Modules\Project\Models\Record;
use Illuminate\Foundation\Events\Dispatchable;

class PreImportEvent
{
    use Dispatchable;

    public Process $process;
    public Record $record;
    public array $params;

    /** @var ProcessFileDto[] */
    public array $files;

    public function __construct(Process $importer, Record $record, array $params, array $files)
    {
        $this->process = $importer;
        $this->record = $record;
        $this->params = $params;
        $this->files = $files;
    }
}
