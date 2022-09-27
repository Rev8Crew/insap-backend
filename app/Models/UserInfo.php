<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model;

/**
 * Class UserInfo
 * @property int user_id
 * @property string avatar
 *
 * @package App\Models
 */
class UserInfo extends BaseMongoModel
{
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
    protected $dates = ['created_at', 'updated_at'];

    protected $guarded = [];
}
