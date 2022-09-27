<?php

namespace App\Modules\Processing\Models\Dto;

use App\Enums\ActiveStatus;
use App\Enums\Process\ProcessInterpreter;
use App\Enums\Process\ProcessType;
use App\Models\User;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Plugins\Models\Plugin;
use App\Modules\Processing\Requests\ProcessCreateRequest;
use App\Traits\Makeable;
use Illuminate\Contracts\Support\Arrayable;

class ProcessDto implements Arrayable
{
    use Makeable;

    public ProcessType $processType;
    public ProcessInterpreter $processInterpreter;
    public ActiveStatus $activeStatus;
    public int $projectId;

    public ?string $name = null;
    public ?string $description = null;

    public ?int $applianceId = null;
    public ?int $pluginId = null;
    public ?int $userId = null;

    public function __construct(ProcessType $processType, ProcessInterpreter $processInterpreter, int $projectId)
    {
        $this->processType = $processType;
        $this->processInterpreter = $processInterpreter;
        $this->activeStatus = ActiveStatus::create(ActiveStatus::ACTIVE);
        $this->projectId = $projectId;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->processType->getValue(),
            'interpreter' => $this->processInterpreter->getValue(),
            'is_active' => $this->activeStatus->getValue(),

            'appliance_id' => $this->applianceId,
            'plugin_id' => $this->pluginId,
            'user_id' => $this->userId,
            'project_id' => $this->projectId,
        ];
    }

    /**
     * @param ProcessCreateRequest $request
     * @return static
     */
    public static function createFromRequest($request) : self
    {
        $processType = ProcessType::create((int)$request->input('type'));
        $processInterpreter = ProcessInterpreter::create((string)$request->input('interpreter'));

        return (new self( $processType, $processInterpreter, (int)$request->input('project_id')))
            ->setName($request->input('name'))
            ->setDescription($request->input('description'))
            ->setUserId(optional($request->user())->id)
            ->setApplianceId($request->input('appliance_id'))
            ->setPluginId($request->input('plugin_id'));
    }

    public function setApplianceId(?int $applianceId): ProcessDto
    {
        $this->applianceId = $applianceId;
        return $this;
    }

    public function setPluginId(?int $pluginId): ProcessDto
    {
        $this->pluginId = $pluginId;
        return $this;
    }

    public function setUserId(?int $userId): ProcessDto
    {
        $this->userId = $userId;
        return $this;
    }

    public function setProcessType(ProcessType $processType): ProcessDto
    {
        $this->processType = $processType;
        return $this;
    }

    public function setName(?string $name): ProcessDto
    {
        $this->name = $name;
        return $this;
    }

    public function setDescription(?string $description): ProcessDto
    {
        $this->description = $description;
        return $this;
    }

    public function setProcessInterpreter(ProcessInterpreter $processInterpreter): ProcessDto
    {
        $this->processInterpreter = $processInterpreter;
        return $this;
    }

    public function setActiveStatus(ActiveStatus $activeStatus): ProcessDto
    {
        $this->activeStatus = $activeStatus;
        return $this;
    }
}
