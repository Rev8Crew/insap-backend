<?php

namespace App\Modules\Importer\Models\Importer;

use App\Enums\ActiveStatus;
use App\Models\User;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Importer\Models\ImporterEvents\ImporterEvent;
use App\Modules\Plugins\Models\Plugin;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Carbon;
use Spatie\Activitylog\Models\Activity;
use Spatie\Activitylog\Traits\LogsActivity;

/**
 * App\Modules\Importer\Models\Importer\Importer
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property int|null $appliance_id
 * @property int $is_active
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Collection|Activity[] $activities
 * @property-read int|null $activities_count
 * @property-read Appliance|null $appliance
 * @property-read User $user
 * @property-read ImporterEvent[] $events
 * @method static Builder|Importer newModelQuery()
 * @method static Builder|Importer newQuery()
 * @method static Builder|Importer query()
 * @method static Builder|Importer whereApplianceId($value)
 * @method static Builder|Importer whereCreatedAt($value)
 * @method static Builder|Importer whereDescription($value)
 * @method static Builder|Importer whereId($value)
 * @method static Builder|Importer whereIsActive($value)
 * @method static Builder|Importer whereName($value)
 * @method static Builder|Importer whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Importer extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'appliance_id',
        'plugin_id',
        'is_active'
    ];

    protected $attributes = [
        'is_active' => ActiveStatus::ACTIVE
    ];

    public function events(): HasMany
    {
        return $this->hasMany(ImporterEvent::class);
    }

    public function appliance(): BelongsTo
    {
        return $this->belongsTo(Appliance::class, 'appliance_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function plugin(): BelongsTo
    {
        return $this->belongsTo(Plugin::class, 'plugin_id');
    }
}
