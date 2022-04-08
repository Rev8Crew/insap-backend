<?php


namespace App\Modules\Project\Services;


use App\Models\User;
use App\Modules\Plugins\Services\PluginService;
use App\Modules\Project\Models\Record;
use App\Modules\Project\Models\RecordData;
use App\Modules\Project\Models\RecordInfo;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use MongoDB\BSON\ObjectId;

/**
 * Class RecordService
 * @package App\Modules\Project\Services
 */
class RecordService
{
    private PluginService $pluginService;

    public function __construct(PluginService $pluginService)
    {
        $this->pluginService = $pluginService;
    }

    /**
     * @param array $input
     * @param RecordData $recordData
     * @param User $user
     * @return Record|Model
     */
    public function create(array $input, RecordData $recordData, User $user) {
        $input['user_id'] = $user->id;
        $input['record_data_id'] = $recordData->id;
        $record = Record::create($input);
        return $record;
    }

    public function delete(Record $record) : bool
    {
        RecordInfo::where('record_id', $record->id)->delete();

        return $record->delete();
    }

    public function deleteRecordsInfo(Record $record) : bool
    {
        // MongoFS
        foreach ($record->files ?? [] as $fileId) {
            if (is_array($fileId)) {
                \MongoGrid::delete( new ObjectId($fileId['$oid']));
                continue;
            }

            \MongoGrid::delete( new ObjectId($fileId));
        }
        return RecordInfo::where('record_id', $record->id)->delete();
    }

    public function getRecordInfo(Record $record) : Collection
    {
        if ($record->importer->plugin) {
            return $this->pluginService->getPluginService($record->importer->plugin)->getQueryBuilder($record)->get();
        }

        return RecordInfo::where('record_id', $record->id)->get();
    }
}
