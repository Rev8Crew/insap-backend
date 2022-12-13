<?php
declare(strict_types=1);

namespace App\Modules\Project\DTO;

use App\Enums\ActiveStatus;
use App\Modules\Project\Requests\RecordCreateRequest;
use App\Traits\Makeable;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class RecordCreateDto implements Arrayable
{
    use Makeable;

    public string $name;

    public ActiveStatus $activeStatus;

    public int $importStatus = 0;

    public int $order = 0;

    public ?string $description = null;
    public ?Carbon $date = null;

    public ?array $files = null;
    public ?array $params = null;

    public ?string $importError = null;

    public ?int $recordDataId = null;
    public ?int $userId = null;
    public ?int $processId = null;
    public ?int $imageId = null;

    /**
     * @param string $name
     * @param ActiveStatus $activeStatus
     */
    public function __construct(string $name, ActiveStatus $activeStatus)
    {
        $this->name = $name;
        $this->activeStatus = $activeStatus;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'activeStatus' => $this->activeStatus->getValue(),
            'date' => $this->date,
            'order' => $this->order,
            'import_status' => $this->importStatus,
            'files' => $this->files,
            'params' => $this->params,
            'import_error' => $this->importError,

            'record_data_id' => $this->recordDataId,
            'user_id' => $this->userId,
            'process_id' => $this->processId,
            'image_id' => $this->imageId,
        ];
    }

    public static function fromRequest(RecordCreateRequest $request): self
    {
        return self::make($request->input('name'), ActiveStatus::create(ActiveStatus::ACTIVE))
            ->setDescription($request->input('description'))
            ->setDate(Carbon::parse($request->input('date')))
            ->setProcessId((int)$request->input('process_id'))
            ->setUserId($request->user()->id)
            ->setRecordDataId((int)$request->input('record_data_id'))
            ->setImageId(0);
    }

    public function setImportStatus(int $importStatus): RecordCreateDto
    {
        $this->importStatus = $importStatus;
        return $this;
    }

    public function setOrder(int $order): RecordCreateDto
    {
        $this->order = $order;
        return $this;
    }

    public function setDescription(?string $description): RecordCreateDto
    {
        $this->description = $description;
        return $this;
    }

    public function setDate(?Carbon $date): RecordCreateDto
    {
        $this->date = $date;
        return $this;
    }

    public function setFiles(?array $files): RecordCreateDto
    {
        $this->files = $files;
        return $this;
    }

    public function setParams(?array $params): RecordCreateDto
    {
        $this->params = $params;
        return $this;
    }

    public function setImportError(?string $importError): RecordCreateDto
    {
        $this->importError = $importError;
        return $this;
    }

    public function setRecordDataId(?int $recordDataId): RecordCreateDto
    {
        $this->recordDataId = $recordDataId;
        return $this;
    }

    public function setUserId(?int $userId): RecordCreateDto
    {
        $this->userId = $userId;
        return $this;
    }

    public function setProcessId(?int $processId): RecordCreateDto
    {
        $this->processId = $processId;
        return $this;
    }

    public function setImageId(?int $imageId): RecordCreateDto
    {
        $this->imageId = $imageId;
        return $this;
    }
}
