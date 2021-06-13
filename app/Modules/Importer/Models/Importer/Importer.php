<?php

namespace App\Modules\Importer\Models\Importer;

use App\helpers\IsActiveHelper;
use App\Models\User;
use App\Modules\Appliance\Models\Appliance;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\UploadedFile;
use Spatie\Activitylog\Traits\LogsActivity;

class Importer extends Model
{
    use HasFactory, LogsActivity;

    public const TEST_IMPORTER_ID = 1;

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
        'description',
        'interpreter_class',
        'appliance_id',
        'user_id',
        'is_active'
    ];

    protected $attributes = [
        'is_active' => IsActiveHelper::ACTIVE_ACTIVE
    ];

    /**
     * Return processed data
     * @param array $params
     * @param UploadedFile[] $files
     * @return array
     */
    public function exec(array $params, array $files) : array {

    }

    /**
     * @return string
     */
    public function getStoragePath(): string
    {
        return \Storage::disk('import')->path($this->id);
    }


    public function appliance() {
        return $this->belongsTo(Appliance::class, 'appliance_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
