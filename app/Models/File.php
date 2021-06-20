<?php

namespace App\Models;

use App\helpers\IsActiveHelper;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;


/**
 * App\Models\File
 *
 * @property int $id
 * @property string|null $path
 * @property string|null $url
 * @property string|null $name
 * @property string|null $mime
 * @property int $is_active
 * @property int|null $user_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read User|null $user
 * @method static Builder|File newModelQuery()
 * @method static Builder|File newQuery()
 * @method static Builder|File query()
 * @method static Builder|File whereCreatedAt($value)
 * @method static Builder|File whereId($value)
 * @method static Builder|File whereIsActive($value)
 * @method static Builder|File whereMime($value)
 * @method static Builder|File whereName($value)
 * @method static Builder|File wherePath($value)
 * @method static Builder|File whereUpdatedAt($value)
 * @method static Builder|File whereUrl($value)
 * @method static Builder|File whereUserId($value)
 * @mixin Eloquent
 */
class File extends Model
{
    use HasFactory, LogsActivity;

    const TEST_FILE_ID = 1;

    /**
     *  Log all fillable attr
     * @var bool
     */
    protected static $logFillable = true;

    /**
     * @var string[]
     */
    protected $fillable = [
        'path',
        'url',
        'name',
        'mime',
        'is_active',
        'user_id'
    ];

    protected $attributes = [
        'is_active' => IsActiveHelper::ACTIVE_ACTIVE
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
