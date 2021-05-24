<?php

namespace App\Modules\Importer\Models\Importer;

use App\Modules\Appliance\Models\Appliance;
use Illuminate\Database\Eloquent\Model;

class Importer extends Model
{
    /**
     * Return processed data
     * @param array $params
     * @param array $files
     * @return array
     */
    public function exec(array $params, array $files) : array {
        // @TODO add method
    }


    public function appliance() {
        return $this->belongsTo(Appliance::class, 'appliance_id');
    }

    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
}
