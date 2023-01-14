<?php
declare(strict_types=1);

namespace App\Modules\Project\DTO;

use App\Enums\Process\ProcessField;

class RecordFieldDto
{
    public string $alias;
    public ProcessField $fieldType;

    public $value;

    public function __construct(string $alias, ProcessField $fieldType, $value)
    {
        $this->alias = $alias;
        $this->fieldType = $fieldType;
        $this->value = $value;
    }

    public function isNotEmpty() : bool
    {
        if ($this->getFieldType()->is(ProcessField::FIELD_CHECKBOX)) {
            return true;
        }

        return (bool)$this->getValue();
    }

    public static function makeFromArray(array $array): self
    {
        return new self($array['alias'], ProcessField::create($array['field_type']), $array['value']);
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getFieldType(): ProcessField
    {
        return $this->fieldType;
    }

    public function getValue()
    {
        return $this->value;
    }
}
