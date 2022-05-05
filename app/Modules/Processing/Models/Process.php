<?php

namespace App\Modules\Processing\Models;

use App\Enums\ActiveStatus;
use App\Models\User;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Plugins\Models\Plugin;
use App\Modules\Processing\Models\Interpreter\InterpreterInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

/**
 * App\Modules\Processing\Models\Process
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property int $type
 * @property string|null $interpreter Interpreter like PHP, python, go ...
 * @property int|null $appliance_id
 * @property int|null $plugin_id
 * @property int|null $user_id
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read Appliance|null $appliance
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Modules\Processing\Models\ProcessFieldType[] $fields
 * @property-read int|null $fields_count
 * @property-read Plugin|null $plugin
 * @property-read User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder|Process newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Process newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Process query()
 * @method static \Illuminate\Database\Eloquent\Builder|Process whereApplianceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Process whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Process whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Process whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Process whereInterpreter($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Process whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Process whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Process wherePluginId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Process whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Process whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Process whereUserId($value)
 * @mixin \Eloquent
 */
class Process extends Model
{
    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'type',
        'interpreter',
        'appliance_id',
        'plugin_id',
        'user_id',
        'is_active'
    ];

    protected $attributes = [
        'is_active' => ActiveStatus::ACTIVE
    ];

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

    public function fields(): HasMany
    {
        return $this->hasMany(ProcessFieldType::class);
    }

    /**
     * @return string
     */
    public function getStoragePath(): string
    {
        return Storage::disk('process')->path($this->id);
    }

    public function getInterpreter(): InterpreterInterface
    {
        return new $this->interpreter;
    }
}
