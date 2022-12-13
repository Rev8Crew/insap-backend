<?php
declare(strict_types=1);

namespace App\Modules\Project\DTO;

use App\Enums\ImporterField;

class RecordFieldDto
{
    public string $alias;
    public ImporterField $fieldType;

    public $value;

    public function __construct(string $alias, ImporterField $fieldType, $value)
    {
        $this->alias = $alias;
        $this->fieldType = $fieldType;
        $this->value = $value;
    }

    public static function makeFromArray(array $array): self
    {
        return new self($array['alias'], ImporterField::create($array['field_type']), $array['value']);
    }

    public function getAlias(): string
    {
        return $this->alias;
    }

    public function getFieldType(): ImporterField
    {
        return $this->fieldType;
    }

    public function getValue()
    {
        return $this->value;
    }
}
