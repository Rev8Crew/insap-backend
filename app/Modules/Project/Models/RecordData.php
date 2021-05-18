<?php

namespace App\Modules\Project\Models;

use App\helpers\IsActiveHelper;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Modules\Project\Models\RecordData
 *
 * @property-read \Illuminate\Database\Eloquent\Collection|\Spatie\Activitylog\Models\Activity[] $activities
 * @property-read int|null $activities_count
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData query()
 * @mixin \Eloquent
 * @property int $id
 * @property string $name Record data name
 * @property string $description Record data description
 * @property string|null $image Record data image
 * @property int $order Record data order
 * @property int $is_active Is record data active
 * @property int $project_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereUpdatedAt($value)
 */
class RecordData extends Model
{
    use HasFactory, LogsActivity;

    const TEST_RECORD_DATA_ID = 1;

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
        'image',
        'order',
        'is_active'
    ];

    protected $attributes = [
        'is_active' => IsActiveHelper::ACTIVE_ACTIVE,
    ];

    public function project(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo( Project::class, 'project_id');
    }

    public function records(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Record::class);
    }
}
