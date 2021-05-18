<?php

namespace App\Modules\Project\Services;

use App\helpers\IsActiveHelper;
use App\Modules\Project\Models\RecordData;

class RecordDataService
{
    /**
     * @param array  $input
     * @param string $imagePath
     *
     * @return RecordData|\Illuminate\Database\Eloquent\Model
     */
    public function create(array $input, string $imagePath = '') : RecordData {
        $input['image'] = $imagePath ? base64_encode(file_get_contents($imagePath)) : '';
        $recordData = RecordData::create($input);
        return $recordData;
    }

    public function delete(RecordData $recordData) {
        $recordData->delete();
    }

    /**
     * @param RecordData $recordData
     *
     * @return RecordData
     */
    public function activate(RecordData $recordData): RecordData
    {
        $recordData->update(['is_active' => IsActiveHelper::ACTIVE_ACTIVE]);
        return $recordData;
    }

    /**
     * @param RecordData $recordData
     *
     * @return RecordData
     */
    public function deactivate(RecordData $recordData): RecordData
    {
        $recordData->update(['is_active' => IsActiveHelper::ACTIVE_INACTIVE]);
        return $recordData;
    }

    /**
     * @param RecordData $recordData
     * @param string     $name
     *
     * @return RecordData
     */
    public function changeName(RecordData $recordData, string $name): RecordData
    {
        $recordData->update(['name' => $name]);
        return $recordData;
    }
}