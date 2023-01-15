<?php
declare(strict_types=1);

namespace Tests\Fixture;

use App\Enums\ActiveStatus;
use App\Models\User;
use App\Modules\Processing\Models\Process;
use App\Modules\Project\DTO\RecordCreateDto;
use App\Modules\Project\Models\RecordData;
use App\Modules\Project\Services\RecordService;
use Illuminate\Foundation\Testing\WithFaker;

class RecordFixture
{
    use WithFaker;

    private RecordService $recordService;

    public function __construct(RecordService $recordService)
    {
        $this->recordService = $recordService;
    }

    public function create(string $name, Process $process, ?RecordData $recordData = null)
    {
        $recordDataId = $recordData->id ?? RecordData::TEST_RECORD_DATA_ID;
        $userId = User::find(User::TEST_USER_ID);

        $dto = RecordCreateDto::make($name, ActiveStatus::create(ActiveStatus::ACTIVE))
            ->setRecordDataId($recordDataId)
            ->setUserId($userId->id)
            ->setProcessId($process->id);

        return $this->recordService->createFromDto($dto);
    }
}
