<?php

namespace App\Modules\Project\Models;

use App\Enums\ActiveStatus;
use App\helpers\ImageHelper;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * App\Modules\Project\Models\Project
 *
 * @property int $id
 * @property string $name Project name
 * @property string $description Project description
 * @property int $order Project order
 * @property int $is_active Is project active
 * @property int|null $image_id Project image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $image
 * @property-read File|null $imageFile
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Project\Models\RecordData[] $recordsData
 * @property-read int|null $records_data_count
 * @property-read \Illuminate\Database\Eloquent\Collection|User[] $users
 * @property-read int|null $users_count
 * @method static \Illuminate\Database\Eloquent\Builder|Project newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Project query()
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Project whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Project extends Model
{
    use HasFactory;

    public const TEST_PROJECT_ID = 1;

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
        'is_active' => ActiveStatus::ACTIVE
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
