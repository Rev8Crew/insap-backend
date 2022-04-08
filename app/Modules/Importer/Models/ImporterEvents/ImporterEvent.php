<?php

namespace App\Modules\Importer\Models\ImporterEvents;

use App\Enums\ActiveStatus;
use App\Modules\Importer\Models\Importer\Importer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Storage;


/**
 * App\Modules\Importer\Models\ImporterEvents\ImporterEvent
 *
 * @property int $id
 * @property string|null $name name for event
 * @property int|null $event list of events
 * @property int $is_active
 * @property string|null $interpreter_class Interpreter like PHP, python, go ...
 * @property int|null $importer_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Importer|null $importer
 * @method static \Illuminate\Database\Eloquent\Builder|ImporterEvent newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImporterEvent newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ImporterEvent query()
 * @method static \Illuminate\Database\Eloquent\Builder|ImporterEvent whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImporterEvent whereEvent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImporterEvent whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImporterEvent whereImporterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImporterEvent whereInterpreterClass($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImporterEvent whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImporterEvent whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ImporterEvent whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ImporterEvent extends Model
{
    use HasFactory;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'event',
        'interpreter_class',
        'is_active',
        'importer_id'
    ];

    protected $attributes = [
        'is_active' => ActiveStatus::ACTIVE
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
