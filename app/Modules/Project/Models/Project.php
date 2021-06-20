<?php

namespace App\Modules\Project\Models;

use App\helpers\ImageHelper;
use App\helpers\IsActiveHelper;
use App\Models\File;
use App\Models\User;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;


/**
 * App\Modules\Project\Models\Project
 *
 * @property int $id
 * @property string $name Project name
 * @property string $description Project description
 * @property int $order Project order
 * @property int $is_active Is project active
 * @property int|null $image_id Project image
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read string $image
 * @property-read File|null $imageFile
 * @property-read Collection|RecordData[] $recordsData
 * @property-read int|null $records_data_count
 * @property-read Collection|User[] $users
 * @property-read int|null $users_count
 * @method static Builder|Project newModelQuery()
 * @method static Builder|Project newQuery()
 * @method static Builder|Project query()
 * @method static Builder|Project whereCreatedAt($value)
 * @method static Builder|Project whereDescription($value)
 * @method static Builder|Project whereId($value)
 * @method static Builder|Project whereImageId($value)
 * @method static Builder|Project whereIsActive($value)
 * @method static Builder|Project whereName($value)
 * @method static Builder|Project whereOrder($value)
 * @method static Builder|Project whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Project extends Model
{
    use HasFactory, LogsActivity;

    const TEST_PROJECT_ID = 1;

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
        'image_id',
        'order',
        'is_active'
    ];

    protected $attributes = [
        'is_active' => IsActiveHelper::ACTIVE_ACTIVE
    ];

    /**
     * Return URL of the image
     * @return string
     */
    public function getImageAttribute() : string {
        return $this->image_id ? File::find($this->image_id)->url : ImageHelper::getAvatarImage($this->name);
    }

    public function imageFile(): BelongsTo
    {
        return $this->belongsTo(File::class, 'image_id');
    }

    public function recordsData(): HasMany
    {
        return $this->hasMany(RecordData::class);
    }

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_project');
    }
}
