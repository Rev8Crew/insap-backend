<?php

namespace App\Modules\Importer\Models\ImporterEvents;

use App\helpers\IsActiveHelper;
use App\Modules\Importer\Models\Importer\Importer;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;
use Storage;


/**
 * App\Modules\Importer\Models\ImporterEvents\ImporterEvent
 *
 * @property int $id
 * @property string|null $name name for event
 * @property int|null $event list of events
 * @property int|null $order
 * @property int $is_active
 * @property string|null $interpreter_class Interpreter like PHP, python, go ...
 * @property int|null $importer_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read Importer|null $importer
 * @method static Builder|ImporterEvent newModelQuery()
 * @method static Builder|ImporterEvent newQuery()
 * @method static Builder|ImporterEvent query()
 * @method static Builder|ImporterEvent whereCreatedAt($value)
 * @method static Builder|ImporterEvent whereEvent($value)
 * @method static Builder|ImporterEvent whereId($value)
 * @method static Builder|ImporterEvent whereImporterId($value)
 * @method static Builder|ImporterEvent whereInterpreterClass($value)
 * @method static Builder|ImporterEvent whereIsActive($value)
 * @method static Builder|ImporterEvent whereName($value)
 * @method static Builder|ImporterEvent whereOrder($value)
 * @method static Builder|ImporterEvent whereUpdatedAt($value)
 * @mixin Eloquent
 */
class ImporterEvent extends Model
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
        'event',
        'interpreter_class',
        'order',
        'is_active',
        'importer_id'
    ];

    protected $attributes = [
        'is_active' => IsActiveHelper::ACTIVE_ACTIVE
    ];

    public function importer(): BelongsTo
    {
        return $this->belongsTo(Importer::class, 'importer_id');
    }

    /**
     * @return string
     */
    public function getStoragePath(): string
    {
        return Storage::disk('import')->path($this->id);
    }
}
