<?php

namespace App\Modules\Project\Models;

use App\Enums\ActiveStatus;
use App\Enums\ImportStatus;
use App\helpers\ImageHelper;
use App\Models\File;
use App\Models\User;
use App\Modules\Processing\Models\Process;
use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


/**
 * App\Modules\Project\Models\Record
 *
 * @property int $id
 * @property string $name Record name
 * @property string $description Record description
 * @property int $order Record order
 * @property int $is_active Is record active
 * @property array|null $files
 * @property array|null $params
 * @property int $record_data_id
 * @property int $user_id
 * @property int $importer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Modules\Project\Models\RecordData|null $recordData
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Record newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Record newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Record query()
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereFiles($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereImporterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereParams($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereRecordDataId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereUserId($value)
 * @mixin \Eloquent
 * @property int $process_id
 * @property-read Process|null $process
 * @method static \Illuminate\Database\Eloquent\Builder|Record whereProcessId($value)
 */
class Record extends Model
{
    use HasFactory, ActiveScope;

    public const TEST_RECORD_ID = 1;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name', // Название записи
        'description', // Описание
        'order', // Порядок отображения
        'is_active',
        'files', // FileIds from MongoFS
        'params',
        'import_status',
        'import_error',
        'record_data_id',
        'user_id',
        'process_id',
        'date',
        'image_id'
    ];

    protected $appends = [
        'records_info'
    ];

    protected $attributes = [
        'is_active' => ActiveStatus::ACTIVE,
        'import_status' => ImportStatus::INITIAL
    ];

    protected $casts = [
        'files' => 'array',
        'params' => 'array',
    ];

    public function recordData(): BelongsTo
    {
        return $this->belongsTo(RecordData::class, 'record_data_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class, 'process_id');
    }

    public function imageFile(): BelongsTo
    {
        return $this->belongsTo(File::class, 'image_id');
    }

    /**
     * Return URL of the image
     * @return string
     */
    public function getImageAttribute() : string {
        return $this->image_id ? File::find($this->image_id)->url : ImageHelper::getAvatarImage($this->name);
    }
}
