<?php


namespace App\Modules\Importer\Models\ImporterEvents;


use App\Enums\ActiveStatus;
use App\Modules\Importer\Models\Importer\Importer;
use Illuminate\Contracts\Support\Arrayable;

class ImporterEventDto implements Arrayable
{
    private ?string $name = null;
    private ImporterEventEvent $event;
    private int $isActive = ActiveStatus::ACTIVE;
    private ImporterEventInterpreter $interpreter;
    private Importer $importer;

    public function __construct(Importer $importer, ImporterEventEvent $event, ImporterEventInterpreter $interpreter)
    {
        $this->event = $event;
        $this->interpreter = $interpreter;
        $this->importer = $importer;
    }

    public function toArray(): array
    {
        return [
            'name' => $this->getName(),
            'event' => $this->getEventInteger(),
            'interpreter_class' => $this->getInterpreterString(),
            'is_active' => $this->getIsActive(),
            'importer_id' => $this->getImporterId()
        ];
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return ImporterEventDto
     */
    public function setName(?string $name): ImporterEventDto
    {
        $this->name = $name;
        return $this;
    }

    public function getEventInteger(): int
    {
        return $this->event->getEvent();
    }

    public function getInterpreterString(): string
    {
        return $this->interpreter->getInterpreter();
    }

    public function getIsActive(): int
    {
        return $this->isActive;
    }

    /**
     * @param int $isActive
     * @return ImporterEventDto
     */
    public function setIsActive(int $isActive): ImporterEventDto
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getImporterId(): int
    {
        return $this->importer->id;
    }
}
