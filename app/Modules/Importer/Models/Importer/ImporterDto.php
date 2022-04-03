<?php


namespace App\Modules\Importer\Models\Importer;


use App\Enums\ActiveStatus;
use App\Modules\Appliance\Models\Appliance;
use App\Modules\Plugins\Models\Plugin;
use Illuminate\Contracts\Support\Arrayable;

/**
 * Class ImporterDto
 * @package App\Modules\Importer\Models\Importer
 */
class ImporterDto implements Arrayable
{
    public string $description = ' ';
    private string $name;
    private int $is_active = ActiveStatus::ACTIVE;

    private Appliance $appliance;
    private ?Plugin $plugin;

    /**
     * ImporterDto constructor.
     * @param string $name
     * @param Appliance $appliance
     * @param string $description
     */
    public function __construct(string $name, Appliance $appliance, string $description = '', Plugin $plugin = null)
    {
        $this->name = $name;
        $this->appliance = $appliance;
        $this->plugin = $plugin;
    }

    /**
     * @return array
     */
    public function toArray(): array
    {
        $array = [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'is_active' => $this->getIsActive(),
            'appliance_id' => $this->getApplianceId(),
        ];

        if ($this->plugin) {
            $array['plugin_id'] = $this->plugin->id;
        }

        return $array;
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

    /**
     * @return Plugin|null
     */
    public function getPlugin(): ?Plugin
    {
        return $this->plugin;
    }
}
