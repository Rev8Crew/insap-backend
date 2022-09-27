<?php
declare(strict_types=1);

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

class BaseMongoModel extends Model
{
    public function getConnection()
    {
        if (config('database.mongodb_test_connection') || app()->environment('testing')) {
            return static::resolveConnection('mongodb_test');
        }

        return parent::getConnection();
    }
}
