<?php

namespace App\Modules\Project\Models;

use App\helpers\IsActiveHelper;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Traits\LogsActivity;


/**
 * App\Modules\Project\Models\Record
 *
 * @property int $id
 * @property string $name Record name
 * @property string $description Record description
 * @property int $order Record order
 * @property int $is_active Is record active
 * @property int $record_data_id
 * @property int $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read \Illuminate\Support\Collection|null $records_info
 * @property-read \App\Modules\Project\Models\RecordData $recordData
 * @property-read User $user
 * @method static \Illuminate\Database\Eloquent\Builder|Record newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Record newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Record query()
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereRecordDataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereUserId($value)
 * @mixin \Eloquent
 */
class Record extends Model
{
    use HasFactory, LogsActivity;

    /**
     *  Log all fillable attr
     * @var bool
     */
    protected static $logFillable = true;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'order',
        'is_active'
    ];

    protected $appends = [
        'records_info'
    ];

    protected $attributes = [
        'is_active' => IsActiveHelper::ACTIVE_ACTIVE
    ];

    /**
     * @return Collection|null
     */
    public function getRecordsInfoAttribute(): ?Collection
    {
        return RecordInfo::where('record_id', $this->id)->get();
    }

    public function recordData(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(RecordData::class, 'record_data_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
