<?php

namespace App\Modules\Plugins\Models;

use App\Modules\Importer\Models\ImporterEvents\ImporterEventParams;
use App\Modules\Project\Models\Record;

interface PluginServiceInterface
{
    public function preprocess(Record $record, ImporterEventParams $eventParams);

    public function getQueryBuilder(Record $record);
}
