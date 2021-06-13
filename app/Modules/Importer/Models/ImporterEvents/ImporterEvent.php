<?php

namespace App\Modules\Importer\Models\ImporterEvents;

use App\Modules\Appliance\Models\Appliance;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImporterEvent extends Model
{
    const EVENT_PRE = 0;
    const EVENT_POST = 1;

    const EVENT_TYPE_PRE = 0;
    const EVENT_TYPE_POST_BEFORE_DB = 1;
    const EVENT_TYPE_POST_AFTER_DB = 2;

    public function appliance(): BelongsTo
    {
        return $this->belongsTo(Appliance::class, 'appliance_id');
    }
}
