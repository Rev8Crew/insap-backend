<?php

namespace App\Modules\Processing\Models\Dto;

use App\Enums\ActiveStatus;
use App\Enums\Process\ProcessInterpreter;
use App\Enums\Process\ProcessType;
use App\Models\User;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Plugins\Models\Plugin;
use Illuminate\Contracts\Support\Arrayable;

class ProcessDto implements Arrayable
{
    public ?string $name = null;
    public ?string $description = null;
    public ProcessType $processType;
    public ProcessInterpreter $processInterpreter;
    public ActiveStatus $activeStatus;

    // TODO
    public Appliance $appliance;
    public ?Plugin $plugin = null;
    public ?User $user = null;

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'description' => $this->description,
            'type' => $this->processType->getValue(),
            'interpreter' => $this->processInterpreter->getValue(),
            'is_active' => $this->activeStatus->getValue(),

            'plugin_id' => optional($this->plugin)->id,
            'user_id' => optional($this->user)->id,
        ];
    }

    /**
     * @param ProcessType $processType
     * @return ProcessDto
     */
    public function setProcessType(ProcessType $processType): ProcessDto
    {
        $this->processType = $processType;
        return $this;
    }

    /**
     * @param string|null $name
     * @return ProcessDto
     */
    public function setName(?string $name): ProcessDto
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param string|null $description
     * @return ProcessDto
     */
    public function setDescription(?string $description): ProcessDto
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @param ProcessInterpreter $processInterpreter
     * @return ProcessDto
     */
    public function setProcessInterpreter(ProcessInterpreter $processInterpreter): ProcessDto
    {
        $this->processInterpreter = $processInterpreter;
        return $this;
    }

    /**
     * @param ActiveStatus $activeStatus
     * @return ProcessDto
     */
    public function setActiveStatus(ActiveStatus $activeStatus): ProcessDto
    {
        $this->activeStatus = $activeStatus;
        return $this;
    }

    /**
     * @param Plugin|null $plugin
     * @return ProcessDto
     */
    public function setPlugin(?Plugin $plugin): ProcessDto
    {
        $this->plugin = $plugin;
        return $this;
    }

    /**
     * @param User|null $user
     * @return ProcessDto
     */
    public function setUser(?User $user): ProcessDto
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @param Appliance $appliance
     * @return ProcessDto
     */
    public function setAppliance(Appliance $appliance): ProcessDto
    {
        $this->appliance = $appliance;
        return $this;
    }
}
