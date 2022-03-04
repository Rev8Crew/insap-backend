<?php

namespace App\Events;

use App\Modules\Importer\Models\Importer\Importer;
use App\Modules\Importer\Models\ImporterEvents\ImporterEventFile;
use App\Modules\Project\Models\Record;
use Illuminate\Foundation\Events\Dispatchable;

class PreImportEvent
{
    use Dispatchable;

    public Importer $importer;
    public Record $record;
    public array $params;
    /** @var ImporterEventFile[]  */
    public array $files;

    public function __construct(Importer $importer,Record $record, array $params, array $files)
    {
        $this->importer = $importer;
        $this->record = $record;
        $this->params = $params;
        $this->files = $files;
    }
}
