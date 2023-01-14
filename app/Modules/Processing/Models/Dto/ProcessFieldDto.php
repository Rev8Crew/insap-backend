<?php
declare(strict_types=1);

namespace App\Modules\Processing\Models\Dto;

use App\Enums\ActiveStatus;
use App\Enums\Process\ProcessField;
use Illuminate\Contracts\Support\Arrayable;

class ProcessFieldDto implements Arrayable
{
    public ProcessField $field;

    public string $alias;
    public string $title;

    public int $order;

    public bool $required;

    public ActiveStatus $activeStatus;

    /** @var mixed|null */
    public $defaultValue;

    public ?string $icon;
    public ?string $description;

    public function __construct(ProcessField $field, string $alias, string $title, int $order, bool $required, ActiveStatus $activeStatus)
    {
        $this->field = $field;
        $this->alias = $alias;
        $this->title = $title;
        $this->order = $order;
        $this->activeStatus = $activeStatus;
    }

    public function toArray(): array
    {
        return [
            'field_type' => $this->field->getValue(),
            'alias' => $this->alias,
            'title' => $this->title,
            'order' => $this->order,
            'activeStatus' => $this->activeStatus->getValue(),
            'icon' => $this->icon,
            'default_value' => $this->defaultValue,
            'description' => $this->description
        ];
    }

    public static function createFromArray(array $fields): self
    {
        return (new self(
            ProcessField::create($fields['field_type']),
            $fields['alias'],
            $fields['title'],
            $fields['order'],
            $fields['required'],
            ActiveStatus::create(ActiveStatus::ACTIVE)
        ))
            ->setDefaultValue($fields['default_value'])
            ->setIcon($fields['icon'])
            ->setDescription($fields['description']);
    }

    public function setDefaultValue($defaultValue)
    {
        $this->defaultValue = $defaultValue;
        return $this;
    }

    public function setIcon(?string $icon): ProcessFieldDto
    {
        $this->icon = $icon;
        return $this;
    }

    public function setDescription(?string $description): ProcessFieldDto
    {
        $this->description = $description;
        return $this;
    }

}
