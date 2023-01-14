<?php

namespace App\Modules\Processing\Models;

use App\Enums\ActiveStatus;
use App\Scopes\ActiveScope;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * App\Modules\Processing\Models\ProcessFieldType
 *
 * @property int $id
 * @property int $field_type
 * @property string $alias
 * @property string $title
 * @property string $description
 * @property string $icon
 * @property int $order
 * @property int $is_active
 * @property int $process_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Modules\Processing\Models\Process|null $process
 * @method static \Illuminate\Database\Eloquent\Builder|ProcessFieldType newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProcessFieldType newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|ProcessFieldType query()
 * @method static \Illuminate\Database\Eloquent\Builder|ProcessFieldType whereAlias($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProcessFieldType whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProcessFieldType whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProcessFieldType whereFieldType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProcessFieldType whereIcon($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProcessFieldType whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProcessFieldType whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProcessFieldType whereOrder($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProcessFieldType whereProcessId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProcessFieldType whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder|ProcessFieldType whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class ProcessFieldType extends Model
{
    use ActiveScope;

    public const REQUIRED_FIELDS = [
        'field_type',
        'alias',
        'title',
        'order',
    ];

    protected $fillable = [
        'field_type',
        'alias',
        'title',
        'description',
        'default_value',
        'icon',
        'order',
        'is_active',
        'process_id'
    ];

    protected $attributes = [
        'is_active' => ActiveStatus::ACTIVE
    ];

    public function process(): BelongsTo
    {
        return $this->belongsTo(Process::class);
    }
}
