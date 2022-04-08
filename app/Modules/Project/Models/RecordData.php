<?php

namespace App\Modules\Project\Models;

use App\Enums\ActiveStatus;
use App\helpers\ImageHelper;
use App\Models\File;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * App\Modules\Project\Models\RecordData
 *
 * @property int $id
 * @property string $name Record data name
 * @property string $description Record data description
 * @property int $order Record data order
 * @property int $is_active Is record data active
 * @property int $project_id
 * @property int|null $image_id Record data image
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read string $image
 * @property-read File|null $imageFile
 * @property-read \App\Modules\Project\Models\Project|null $project
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Project\Models\Record[] $records
 * @property-read int|null $records_count
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData query()
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereImageId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereProjectId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|RecordData whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class RecordData extends Model
{
    use HasFactory;

    public const TEST_RECORD_DATA_ID = 1;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'order',
        'is_active',
        'image_id'
    ];

    protected $attributes = [
        'is_active' => ActiveStatus::ACTIVE,
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function records(): HasMany
    {
        return $this->hasMany(Record::class);
    }

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
}
