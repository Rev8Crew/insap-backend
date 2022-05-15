<?php

namespace Plugins\adcp\Services;

use App\Modules\Importer\Models\ImporterEvents\ImporterEventParams;
use App\Modules\Plugins\Models\PluginServiceInterface;
use App\Modules\Processing\Models\Dto\ProcessParamsDto;
use App\Modules\Project\Models\Record;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Plugins\adcp\Models\Adcp;

class ProcessingService implements PluginServiceInterface
{

    public function preprocess(Record $record, ProcessParamsDto $paramsDto)
    {
        $data = collect($paramsDto->getData());

        $data = $data->map(function ($item) use ($record) {
            $item['depths'] = is_array($item['depths']) ? json_encode($item['depths'], JSON_THROW_ON_ERROR) : $item['depths'];
            $item['record_id'] = $record->id;
            return $item;
        });

        $this->validateData($data->first());

        try {
            DB::beginTransaction();

            /** @var Collection $chunk */
            foreach ($data->chunk(10) as $chunk) {
                Adcp::insert($chunk->toArray());
            }

            DB::commit();
        } catch (\Throwable $throwable) {
            DB::rollBack();
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
        return Adcp::query()->whereRecordId($record->id)->orderBy('step_id');
    }
}
