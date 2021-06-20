<?php


namespace App\Modules\Importer\Models\Importer;


use App\helpers\IsActiveHelper;
use App\Modules\Appliance\Models\Appliance;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class ImporterDto
 * @package App\Modules\Importer\Models\Importer
 */
class ImporterDto implements Arrayable
{
    public string $description = ' ';
    private string $name;
    private int $is_active = IsActiveHelper::ACTIVE_ACTIVE;

    private Appliance $appliance;

    /**
     * ImporterDto constructor.
     * @param string $name
     * @param Appliance $appliance
     */
    public function __construct(string $name, Appliance $appliance)
    {
        $this->name = $name;
        $this->appliance = $appliance;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'is_active' => $this->getIsActive(),
            'appliance_id' => $this->getApplianceId()
        ];
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getIsActive(): int
    {
        return $this->is_active;
    }

    public function getApplianceId(): int
    {
        return $this->appliance->id;
    }
}
