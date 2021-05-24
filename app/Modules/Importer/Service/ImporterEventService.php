<?php


namespace App\Modules\Importer\Service;


use App\Modules\Appliance\Models\Appliance;
use App\Modules\Importer\Models\ImporterEvent\ImporterEvent;
use App\Modules\Importer\Models\ImporterEvent\ImporterEventInterface;

class ImporterEventService
{
    /**
     * @param int $event
     * @param int $eventType
     * @param Appliance $appliance
     * @param array $params
     * @param array $files
     * @param array $processedData
     * @return bool
     */
    public function event(int $event, int $eventType, Appliance $appliance,
                          array $params, array $files, array $processedData = []): bool
    {
        $importerEvents = ImporterEvent::where('event', $event)
            ->where('type', $eventType)
            ->where('appliance_id', $appliance->id);

        foreach ($importerEvents as $importerEvent) {
            /**
             * @var $eventClass ImporterEventInterface
             */
            $eventClass = $importerEvent->event_class;

            $eventClass->setParams($params);
            $eventClass->setFiles($files);
            $eventClass->setProcessedData($processedData);

            $eventClass->run();

        }

        return true;
    }
}
