<?php

namespace Plugins\adcp\Services;

use App\Modules\Importer\Models\ImporterEvents\ImporterEventParams;
use App\Modules\Plugins\Models\PluginServiceInterface;
use App\Modules\Project\Models\Record;
use Illuminate\Support\Collection;
use Plugins\adcp\Models\Adcp;

class ProcessingService implements PluginServiceInterface
{

    public function preprocess(Record $record, ImporterEventParams $eventParams)
    {
        $data = collect($eventParams->getData());

        $data = $data->map( function ($item) use ($record) {
            $item['record_id'] = $record->id;
            return $item;
        });

        $this->validateData($data->first());

        foreach ($data->chunk(1000) as $chunk) {
            Adcp::insert($chunk->toArray());
        }
    }

    /**
     * @throws \JsonException
     */
    public function validateData(array $data): void
    {
        if (
            isset($data['record_id']) === false ||
            isset($data['max_depth']) === false
        ) {
            throw new \RuntimeException('Failed to validate data...' . json_encode($data, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT));
        }
    }

    public function getQueryBuilder(Record $record)
    {
        return Adcp::query()->whereRecordId($record->id);
    }
}
