<?php

namespace App\Modules\Appliance\Models;

use App\Enums\ActiveStatus;
use App\Models\User;
use App\Modules\Project\Models\Project;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Carbon;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;


class Appliance extends Model
{
    use HasFactory, SoftDeletes;

    public const APPLIANCE_TEST_ID = 1;

    /**
     * @var string[]
     */
    protected $fillable = [
        'name',
        'description',
        'is_active',
        'user_id'
    ];

    protected $attributes = [
        'is_active' => ActiveStatus::ACTIVE
    ];

    public function creatorUser(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }
}
