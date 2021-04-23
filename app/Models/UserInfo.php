<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\SoftDeletes;

/**
 * Class UserInfo
 * @property int user_id
 * @property string avatar
 *
 * @package App\Models
 */
class UserInfo extends Model
{
    use SoftDeletes;

    /**
     * @var string Collection name
     */
    protected $collection = 'user_infos';

    /**
     * @var string Connection name
     */
    protected $connection = 'mongodb';

    /**
     * @var string[]
     */
    protected $dates = ['deleted_at', 'created_at', 'updated_at'];

    protected $guarded = [];
}
