<?php

namespace App\Modules\Project\Models;

use Eloquent;
use Illuminate\Support\Collection;
use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Class RecordInfo
 * @package App\Modules\Project\Models
 *
 * @mixin Eloquent
 */
class RecordInfo extends Model
{
    /**
     * @var string Collection name
     */
    protected $collection = 'records';

    /**
     * @var string Connection name
     */
    protected $connection = 'mongodb';

    /**
     * @var string[]
     */
    protected $dates = ['created_at', 'updated_at'];

    protected $guarded = [];

    /**
     * @return Record
     */
    public function getRecordAttribute(): ?Collection
    {
        return Record::where('record_id', $this->record_id)->first();
    }
}
