<?php

namespace App\Modules\Importer\Models\Importer;

use App\Enums\ActiveStatus;
use App\Models\User;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Importer\Models\ImporterEvents\ImporterEvent;
use App\Modules\Plugins\Models\Plugin;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


/**
 * App\Modules\Importer\Models\Importer\Importer
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property int|null $appliance_id
 * @property int|null $plugin_id
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Appliance|null $appliance
 * @property-read \Illuminate\Database\Eloquent\Collection|ImporterEvent[] $events
 * @property-read int|null $events_count
 * @property-read Plugin|null $plugin
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Importer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Importer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Importer query()
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereApplianceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Importer wherePluginId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Importer whereUpdatedAt($value)
 * @mixin \Eloquent
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
