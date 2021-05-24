<?php

namespace App\Modules\Project\Models;

use App\helpers\IsActiveHelper;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Spatie\Activitylog\Models\Activity;
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read Collection|null $records_info
 * @property-read RecordData $recordData
 * @property-read User $user
 * @method static Builder|Record newModelQuery()
 * @method static Builder|Record newQuery()
 * @method static Builder|Record query()
 * @method static Builder|Record whereCreatedAt($value)
 * @method static Builder|Record whereDescription($value)
 * @method static Builder|Record whereId($value)
 * @method static Builder|Record whereIsActive($value)
 * @method static Builder|Record whereName($value)
 * @method static Builder|Record whereOrder($value)
 * @method static Builder|Record whereRecordDataId($value)
 * @method static Builder|Record whereUpdatedAt($value)
 * @method static Builder|Record whereUserId($value)
 * @mixin Eloquent
 */
class Record extends Model
{
    use HasFactory, LogsActivity;


    const TEST_RECORD_ID = 1;
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

    public function recordData(): BelongsTo
    {
        return $this->belongsTo(RecordData::class, 'record_data_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
