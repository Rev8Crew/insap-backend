<?php

namespace App\Modules\Appliance\Models;

use App\Enums\ActiveStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Modules\Appliance\Models\Appliance
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $description
 * @property int $is_active Is project active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance query()
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|Appliance whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Appliance extends Model
{
    use HasFactory;

    public const APPLIANCE_TEST_ID = 1;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'is_active'
    ];

    protected $attributes = [
        'is_active' => ActiveStatus::ACTIVE
    ];
}
